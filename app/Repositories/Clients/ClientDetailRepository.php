<?php

namespace App\Repositories\Clients;

use App\Models\Client;
use App\Repositories\Contracts\Clients\ClientDetailRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ClientDetailRepository implements ClientDetailRepositoryInterface
{
    /**
     * Find client with details
     */
    public function find(int $id): Client
    {
        return Client::where('user_id', auth()->id())
            ->findOrFail($id);
    }

    /**
     * Get client projects with optimized query
     */
    public function getClientProjects(int $clientId): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\Project::query()
            ->where('client_id', $clientId)
            ->whereHas('client', function ($query){
                $query->where('user_id', auth()->id());
            })
            ->withCount('events as project_events_count')
            ->withSum(['events as project_billing_total' => function ($query) {
                $query->where('event_type', 'billing');
            }], 'amount')
            ->withExists('events as has_overdue_events', function ($query) {
                $query->where('is_overdue', true);
            })
            ->withExists('events as has_payment_overdue', function ($query) {
                $query->where('is_payment_overdue', true);
            })
            ->orderBy('projects.start_date', 'desc')
            ->get();
    }

    /**
     * Get filtered client events
     */
    public function getFilteredClientEvents(int $clientId, array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = \App\Models\Event::query()
            ->select([
                'events.*',
                'projects.name as project_name',
                'projects.id as project_id'
            ])
            ->join('projects', 'events.project_id', '=', 'projects.id')
            ->where('projects.client_id', $clientId)
            ->whereRelation('project.client', 'user_id', auth()->id());

        // Appliquer le filtre de statut des événements
        if (isset($filters['event_status_filter'])) {
            $statusFilter = $filters['event_status_filter'];
            switch ($statusFilter) {
                case 'todo':
                    $query->whereIn('events.status', ['todo', 'to_send']);
                    break;
                case 'done':
                    $query->whereIn('events.status', ['done', 'sent']);
                    break;
                // 'all' ne nécessite aucun filtre
            }
        }

        return $query->orderByRaw('COALESCE(events.execution_date, events.send_date) ASC')->get();
    }

    /**
     * Get financial statistics with optimized queries
     */
    public function getFinancialStats(int $clientId): array
    {
        $baseQuery = \App\Models\Event::query()
            ->join('projects', 'events.project_id', '=', 'projects.id')
            ->where('projects.client_id', $clientId)
            ->whereRelation('project.client', 'user_id', auth()->id()) // Sécurité
            ->where('events.event_type', 'billing');

        return [
            'total_billed' => (clone $baseQuery)
                ->whereNot('events.status', 'cancelled')
                ->sum('events.amount') ?? 0,

            'total_paid' => (clone $baseQuery)
                ->where('events.payment_status', 'paid')
                ->sum('events.amount') ?? 0,

            'total_pending' => (clone $baseQuery)
                ->where('events.status', 'to_send')
                ->sum('events.amount') ?? 0,

            'total_sent' => (clone $baseQuery)
                    ->where('events.status', 'sent')
                    ->sum('events.amount') ?? 0,

            'total_upcoming_payment' => (clone $baseQuery)
                ->where('events.status', 'sent')
                ->where('events.payment_status', 'pending')
                ->where('events.payment_due_date', '>=', now())
                ->sum('events.amount') ?? 0,

            'total_overdue_payment' => (clone $baseQuery)
                ->where('events.status', 'sent')
                ->where('events.payment_status', 'pending')
                ->where('events.payment_due_date', '<', now())
                ->sum('events.amount') ?? 0,
        ];
    }

    /**
     * Get project statistics for a client
     */
    public function getProjectStats(int $clientId): array
    {
        $stats = \App\Models\Project::query()
            ->where('client_id', $clientId)
            ->whereRelation('client', 'user_id', auth()->id())
            ->selectRaw('
                COUNT(*) as total_projects,
                SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active_projects,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_projects,
                SUM(CASE WHEN status = "on_hold" THEN 1 ELSE 0 END) as on_hold_projects
            ')
            ->first();

        return [
            'total_projects' => $stats->total_projects ?? 0,
            'active_projects' => $stats->active_projects ?? 0,
            'completed_projects' => $stats->completed_projects ?? 0,
            'on_hold_projects' => $stats->on_hold_projects ?? 0,
        ];
    }
}
