<?php

namespace App\Services\Projects;

use App\Repositories\Contracts\Projects\ProjectListRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ProjectListService
{
    public function __construct(
        private readonly ProjectListRepositoryInterface $projectListRepository
    ) {}

    /**
     * Get skeleton data for immediate rendering
     */
    public function getSkeletonData(int $perPage = 15): array
    {
        return [
            'projects' => [
                'data' => [],
                'current_page' => 1,
                'per_page' => $perPage,
                'total' => 0,
                'last_page' => 1,
                'from' => null,
                'to' => null,
            ],
            'statistics' => [
                'total_projects' => 0,
                'active_projects' => 0,
                'completed_projects' => 0,
                'total_budget' => 0,
                'total_billed' => 0,
                'total_paid' => 0,
                'overdue_projects' => 0,
                'projects_with_overdue_payments' => 0,
            ],
            'clients' => [],
        ];
    }

    /**
     * Get complete data with pagination, statistics and clients
     * OPTIMIZED: Manual transformation to avoid model accessors and N+1 queries
     */
    public function getCompleteData(array $filters = [], int $perPage = 15): array
    {
        try {
            $projects = $this->projectListRepository->paginate($perPage, $filters);

            // Transform projects data to match frontend expectations
            // without triggering model accessors
            $projectsData = $projects->getCollection()->map(function ($project) {
                return $this->transformProjectForFrontend($project);
            })->toArray();

            return [
                'projects' => [
                    'data' => $projectsData,
                    'meta' => [
                        'current_page' => $projects->currentPage(),
                        'last_page' => $projects->lastPage(),
                        'per_page' => $projects->perPage(),
                        'total' => $projects->total(),
                    ],
                ],
                'stats' => $this->projectListRepository->getGlobalStatistics(),
                'clients' => $this->projectListRepository->getAvailableClients(),
                'skeleton_mode' => false,
                'filters' => $filters,
            ];
        } catch (\Exception $e) {
            throw new \Exception('Erreurs lors du chargement des données.');
        }
    }

    /**
     * Transform Project model to match frontend ProjectDTO expectations
     * without creating DTO objects or triggering accessors (performance optimization)
     */
    private function transformProjectForFrontend($project): array
    {
        // Status labels
        $statusLabels = [
            'active' => 'Actif',
            'completed' => 'Terminé',
            'on_hold' => 'En pause',
            'cancelled' => 'Annulé',
        ];

        // Calculate budget_progress (avoid accessor)
        $budgetProgress = 0;
        if ($project->budget && $project->budget > 0) {
            $budgetProgress = $project->total_billed / $project->budget * 100;
        }

        // Calculate budget_exceeded (avoid accessor)
        $budgetExceeded = false;
        if ($project->budget && $project->budget > 0) {
            $budgetExceeded = $project->total_billed > $project->budget;
        }

        // Calculate is_overdue (avoid accessor)
        $isOverdue = $project->status === 'active' &&
                     $project->end_date &&
                     $project->end_date->isPast();

        return [
            'id' => $project->id,
            'client_id' => $project->client_id,
            'name' => $project->name,
            'description' => $project->description,
            'status' => $project->status,
            'budget' => $project->budget,
            'start_date' => $project->start_date?->format('Y-m-d'),
            'end_date' => $project->end_date?->format('Y-m-d'),
            'created_at' => $project->created_at?->toISOString(),
            'updated_at' => $project->updated_at?->toISOString(),

            // Relations (already loaded via with())
            'client' => $project->client ? [
                'id' => $project->client->id,
                'name' => $project->client->name,
                'company' => $project->client->company,
            ] : null,

            // Compteurs (already loaded via withCount())
            'events_count' => $project->events_count ?? 0,
            'completed_tasks_count' => $project->completed_tasks_count ?? 0,
            'pending_tasks_count' => $project->pending_tasks_count ?? 0,

            // Calculs financiers (already loaded via addSelect())
            'total_billed' => $project->total_billed ?? 0,
            'total_paid' => $project->total_paid ?? 0,
            'total_unpaid' => $project->total_unpaid ?? 0,

            // Calculs dérivés (calculés ici pour éviter les accesseurs)
            'budget_progress' => $budgetProgress,
            'budget_exceeded' => $budgetExceeded,

            // Statuts booléens (already loaded via addSelect() ou calculés ici)
            'is_overdue' => $isOverdue,
            'has_overdue_events' => (bool) ($project->has_overdue_events ?? false),
            'has_payment_overdue' => (bool) ($project->has_payment_overdue ?? false),

            // Labels (calculés directement)
            'status_label' => $statusLabels[$project->status] ?? $project->status,
            'formatted_budget' => $project->budget ? number_format($project->budget, 2, ',', ' ').' €' : null,
            'formatted_billed' => number_format($project->total_billed ?? 0, 2, ',', ' ').' €',
            'formatted_paid' => number_format($project->total_paid ?? 0, 2, ',', ' ').' €',
            'formatted_unpaid' => number_format($project->total_unpaid ?? 0, 2, ',', ' ').' €',
        ];
    }

    /**
     * Get paginated projects with filters (legacy method - keep for compatibility)
     */
    public function getPaginatedProjects(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->projectListRepository->paginate($perPage, $filters);
    }

    /**
     * Get global project statistics
     */
    public function getGlobalStatistics(): array
    {
        return $this->projectListRepository->getGlobalStatistics();
    }

    /**
     * Get all clients available for project filters
     */
    public function getAvailableClients(): array
    {
        return $this->projectListRepository->getAvailableClients();
    }
}
