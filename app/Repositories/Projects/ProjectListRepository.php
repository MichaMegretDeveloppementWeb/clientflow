<?php

namespace App\Repositories\Projects;

use App\Models\Project;
use App\Models\Client;
use App\Repositories\Contracts\Projects\ProjectListRepositoryInterface;
use App\Enums\ProjectStatus;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ProjectListRepository implements ProjectListRepositoryInterface
{
    /**
     * Get paginated projects
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Project::withOptimizedRelations();

        // Apply filters
        $query = $this->applyFilters($query, $filters);

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Get global project statistics
     */
    public function getGlobalStatistics(): array
    {
        $projects = Project::query()->whereRelation('client', 'user_id', auth()->id())->get();

        return [
            'active' => $projects->where('status', ProjectStatus::Active->value)->count(),
            'completed' => $projects->where('status', ProjectStatus::Completed->value)->count(),
            'on_hold' => $projects->where('status', ProjectStatus::OnHold->value)->count(),
            'cancelled' => $projects->where('status', ProjectStatus::Cancelled->value)->count(),
            'total' => $projects->count(),
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
        if (!empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
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
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Client filter
        if (!empty($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        // Overdue tasks filter
        if (!empty($filters['has_overdue_tasks'])) {
            $query->whereHas('events', function ($q) {
                $q->where('event_type', 'step')
                  ->where('status', 'todo')
                  ->whereDate('execution_date', '<', now());
            });
        }

        // Payment overdue filter
        if (!empty($filters['has_payment_overdue'])) {
            $query->whereHas('events', function ($q) {
                $q->where('event_type', 'billing')
                  ->where('payment_status', 'pending')
                  ->whereDate('payment_due_date', '<', now());
            });
        }

        return $query;
    }
}
