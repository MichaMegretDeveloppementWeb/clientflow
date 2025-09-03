<?php

namespace App\Repositories\Projects;

use App\Enums\ProjectStatus;
use App\Models\Client;
use App\Models\Event;
use App\Models\Project;
use App\Repositories\Contracts\Projects\ProjectListRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ProjectListRepository implements ProjectListRepositoryInterface
{
    /**
     * Get paginated projects
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        // Requête optimisée : réutiliser withOptimizedRelations qui est déjà bien fait
        // mais s'assurer que toutes les données nécessaires sont pré-calculées
        $query = Project::select('projects.*')
            ->whereRelation('client', 'user_id', auth()->id())
            ->with([
                'client:id,name,company,email',
            ])
            ->withCount([
                'events as events_count',
                'events as completed_tasks_count' => function ($query) {
                    $query->where('event_type', 'step')->where('status', 'done');
                },
                'events as pending_tasks_count' => function ($query) {
                    $query->where('event_type', 'step')->where('status', 'todo');
                },
            ])
            ->addSelect([
                'total_billed' => Event::select(\DB::raw('COALESCE(SUM(amount), 0)'))
                    ->whereColumn('project_id', 'projects.id')
                    ->where('event_type', 'billing')
                    ->whereNotIn('status', ['cancelled']),
                'total_paid' => Event::select(\DB::raw('COALESCE(SUM(amount), 0)'))
                    ->whereColumn('project_id', 'projects.id')
                    ->where('event_type', 'billing')
                    ->where('payment_status', 'paid'),
                'total_unpaid' => Event::select(\DB::raw('COALESCE(SUM(amount), 0)'))
                    ->whereColumn('project_id', 'projects.id')
                    ->where('event_type', 'billing')
                    ->where('payment_status', 'pending'),
                'has_overdue_events' => Event::select(\DB::raw('COUNT(*) > 0'))
                    ->whereColumn('project_id', 'projects.id')
                    ->where(function ($q) {
                        $q->where(function ($sub) {
                            $sub->where('event_type', 'step')
                                ->where('status', 'todo')
                                ->whereDate('execution_date', '<', now()->toDateString());
                        })
                            ->orWhere(function ($sub) {
                                $sub->where('event_type', 'billing')
                                    ->where('status', 'to_send')
                                    ->whereDate('send_date', '<', now()->toDateString());
                            });
                    }),
                'has_payment_overdue' => Event::select(\DB::raw('COUNT(*) > 0'))
                    ->whereColumn('project_id', 'projects.id')
                    ->where('event_type', 'billing')
                    ->where('payment_status', 'pending')
                    ->whereDate('payment_due_date', '<', now()->toDateString()),
            ]);

        // Apply filters
        $query = $this->applyFilters($query, $filters);

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Get global project statistics - OPTIMIZED with aggregate queries
     */
    public function getGlobalStatistics(): array
    {
        // Requête optimisée avec agrégation directe en base
        $baseQuery = Project::whereRelation('client', 'user_id', auth()->id());

        return [
            'active' => (clone $baseQuery)->where('status', ProjectStatus::Active->value)->count(),
            'completed' => (clone $baseQuery)->where('status', ProjectStatus::Completed->value)->count(),
            'on_hold' => (clone $baseQuery)->where('status', ProjectStatus::OnHold->value)->count(),
            'cancelled' => (clone $baseQuery)->where('status', ProjectStatus::Cancelled->value)->count(),
            'total' => $baseQuery->count(),
        ];
    }

    /**
     * Get available clients for dropdown/filters
     */
    public function getAvailableClients(): array
    {
        return Client::select('id', 'name')
            ->where('user_id', auth()->id())
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    /**
     * Apply filters to query
     */
    private function applyFilters(Builder $query, array $filters): Builder
    {
        // Search filter
        if (! empty($filters['search'])) {
            $searchTerm = '%'.$filters['search'].'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('description', 'like', $searchTerm)
                    ->orWhereHas('client', function ($clientQuery) use ($searchTerm) {
                        $clientQuery->where('name', 'like', $searchTerm)
                            ->orWhere('company', 'like', $searchTerm);
                    });
            });
        }

        // Status filter
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Client filter
        if (! empty($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        // Overdue tasks filter
        if (! empty($filters['has_overdue_tasks'])) {
            $query->whereHas('events', function ($q) {
                $q->where('event_type', 'step')
                    ->where('status', 'todo')
                    ->whereDate('execution_date', '<', now());
            });
        }

        // Payment overdue filter
        if (! empty($filters['has_payment_overdue'])) {
            $query->whereHas('events', function ($q) {
                $q->where('event_type', 'billing')
                    ->where('payment_status', 'pending')
                    ->whereDate('payment_due_date', '<', now());
            });
        }

        return $query;
    }
}
