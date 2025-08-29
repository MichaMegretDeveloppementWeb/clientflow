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
     */
    public function getCompleteData(array $filters = [], int $perPage = 15): array
    {
        try {
            $projects = $this->projectListRepository->paginate($perPage, $filters);

            return [
                'projects' => [
                    'data' => $projects->getCollection(),
                    'meta' => [
                        'current_page' => $projects->currentPage(),
                        'last_page' => $projects->lastPage(),
                        'per_page' => $projects->perPage(),
                        'total' => $projects->total(),
                    ]
                ],
                'statistics' => $this->projectListRepository->getGlobalStatistics(),
                'clients' => $this->projectListRepository->getAvailableClients(),
                'skeleton_mode' => false,
            ];
        }catch (\Exception $e) {
            throw new \Exception("Erreurs lors du chargement des données.");
        }
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
