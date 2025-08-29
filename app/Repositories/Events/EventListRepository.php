<?php

namespace App\Repositories\Events;

use App\Enums\EventType;
use App\Enums\PaymentStatus;
use App\Models\Event;
use App\Models\Client;
use App\Models\Project;
use App\Repositories\Contracts\Events\EventListRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class EventListRepository implements EventListRepositoryInterface
{
    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator
    {
        $query = Event::withOptimizedRelations();

        // Apply filters
        $query = $this->applyFilters($query, $filters);

        // Apply sorting
        $sortBy = $filters['sort'] ?? 'created_date';
        $sortOrder = $filters['direction'] ?? 'desc';

        $this->applySorting($query, $sortBy, $sortOrder);

        return $query->paginate($perPage);
    }


    /**
     * Get global statistics (suivant le pattern des autres repositories)
     */
    public function getGlobalStatistics(): array
    {
        // Total événements
        $totalEvents = Event::count();

        // Événements à faire (todo + to_send)
        $todoEvents = Event::whereIn('status', ['todo', 'to_send'])->count();

        // Événements terminés (done + sent)
        $doneEvents = Event::whereIn('status', ['done', 'sent'])->count();

        // Événements en retard
        $overdueEvents = Event::overdue()->count();

        return [
            'total' => $totalEvents,
            'todo' => $todoEvents,
            'done' => $doneEvents,
            'overdue' => $overdueEvents,
        ];
    }

    private function applyFilters($query, array $filters)
    {
        // Filter by event type
        if (!empty($filters['event_type'])) {
            $query->where('event_type', $filters['event_type']);
        }

        // Filter by status
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'todo') {
                // "À faire" inclut todo + to_send (cohérent avec les stats)
                $query->whereIn('status', ['todo', 'to_send']);
            } elseif ($filters['status'] === 'done') {
                // "Terminé" inclut done + sent (cohérent avec les stats)
                $query->whereIn('status', ['done', 'sent']);
            } elseif ($filters['status'] === 'overdue') {
                // "En retard" utilise le scope overdue
                $query->overdue();
            } else {
                $query->where('status', $filters['status']);
            }
        }

        // Filter by payment status
        if (!empty($filters['payment_status'])) {
            if ($filters['payment_status'] === 'overdue') {
                $query->paymentOverdue();
            } else {
                $query->where('payment_status', $filters['payment_status']);
            }
        }

        // Filter by project
        if (!empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }

        // Filter by client
        if (!empty($filters['client_id'])) {
            $query->whereHas('project', function ($q) use ($filters) {
                $q->where('client_id', $filters['client_id']);
            });
        }

        // Filter by search term
        if (!empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('description', 'like', $searchTerm)
                    ->orWhere('type', 'like', $searchTerm)
                    ->orWhereHas('project', function ($projectQuery) use ($searchTerm) {
                        $projectQuery->where('name', 'like', $searchTerm)
                            ->orWhereHas('client', function ($clientQuery) use ($searchTerm) {
                                $clientQuery->where('name', 'like', $searchTerm)
                                    ->orWhere('company', 'like', $searchTerm);
                            });
                    });
            });
        }


        // Filter payment overdue events (billing events only)
        if (isset($filters['payment_overdue']) &&
            ($filters['payment_overdue'] === true || $filters['payment_overdue'] === 'true' || $filters['payment_overdue'] === '1')) {
            $query->where('event_type', EventType::Billing->value)
                ->where('payment_status', PaymentStatus::Pending->value)
                ->whereDate('payment_due_date', '<', now());
        }

        return $query;
    }


    /**
     * Apply sorting to query
     */
    private function applySorting($query, string $sortBy, string $sortOrder): void
    {
        switch ($sortBy) {
            case 'due_date':
                // Tri par date d'échéance (execution_date pour steps, send_date pour billing)
                $query->orderByRaw('COALESCE(execution_date, send_date) ' . $sortOrder);
                break;

            case 'execution_date':
            case 'send_date':
                // Anciens filtres - rediriger vers due_date pour compatibilité
                $query->orderByRaw('COALESCE(execution_date, send_date) ' . $sortOrder);
                break;

            case 'name':
                $query->orderBy('name', $sortOrder);
                break;

            case 'created_date':
            case 'created_at':
                $query->orderBy('created_date', $sortOrder);
                break;

            case 'amount':
                // Tri par montant (seulement pour les facturations, les autres à la fin)
                $query->orderByRaw('amount IS NULL, amount ' . $sortOrder);
                break;

            default:
                $query->orderBy('created_date', $sortOrder);
                break;
        }
    }

    /**
     * Get available clients for dropdown/filters
     */
    public function getAvailableClients(): array
    {
        return Client::select('id', 'name', 'company')
            ->where('user_id', auth()->id())
            ->orderBy('name')
            ->get()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->company ? $client->name . ' (' . $client->company . ')' : $client->name
                ];
            })
            ->toArray();
    }

    public function getAvailableProjects(): array
    {
        return Project::select('id', 'name', 'client_id')
            ->orderBy('name')
            ->get()
            ->map(function ($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'client_id' => $project->client_id
                ];
            })
            ->toArray();
    }
}
