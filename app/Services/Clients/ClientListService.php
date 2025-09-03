<?php

namespace App\Services\Clients;

use App\Repositories\Contracts\Clients\ClientListRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ClientListService
{
    public function __construct(
        private readonly ClientListRepositoryInterface $clientListRepository
    ) {}

    /**
     * Get paginated clients with filters
     */
    public function getPaginatedClients(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->clientListRepository->paginate($perPage, $filters);
    }

    /**
     * Get global statistics for clients
     */
    public function getGlobalStatistics(): array
    {
        return $this->clientListRepository->getGlobalStatistics();
    }

    /**
     * Get skeleton data structure
     */
    public function getSkeletonData(int $perPage = 10): array
    {
        return [
            'clients' => [
                'data' => [],
                'links' => [],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $perPage,
                    'total' => 0,
                ],
            ],
            'stats' => [
                'total' => 0,
                'with_projects' => 0,
                'without_projects' => 0,
                'with_active_projects' => 0,
            ],
            // 'filters' => [] // Supprimé - les filtres sont gérés dans le contrôleur
        ];
    }

    /**
     * Get complete data for clients list (clients + stats)
     */
    public function getCompleteData(array $filters, int $perPage): array
    {

        try {

            $clients = $this->getPaginatedClients($filters, $perPage);
            $stats = $this->getGlobalStatistics();

            return [
                'clients' => [
                    'data' => $clients->getCollection()->map(function ($client) {
                        return [
                            'id' => $client->id,
                            'name' => $client->name,
                            'company' => $client->company,
                            'email' => $client->email,
                            'phone' => $client->phone,
                            'address' => $client->address,
                            'notes' => $client->notes,
                            'projects_count' => $client->getAttribute('projects_count') ?? 0,
                            'active_projects_count' => $client->getAttribute('active_projects_count') ?? 0,
                            'total_revenue' => $client->getAttribute('total_revenue') ?? 0,
                            'pending_amount' => $client->getAttribute('pending_amount') ?? 0,
                            'has_overdue_payments' => $client->getAttribute('has_overdue_payments') ?? false,
                            'latest_project' => $client->latest_project ? [
                                'id' => $client->latest_project->id,
                                'name' => $client->latest_project->name,
                                'status' => $client->latest_project->status,
                                'created_at' => $client->latest_project->created_at?->toISOString(),
                            ] : null,
                            'created_at' => $client->created_at?->toISOString(),
                            'updated_at' => $client->updated_at?->toISOString(),
                        ];
                    }),
                    'links' => $clients->linkCollection(),
                    'meta' => [
                        'current_page' => $clients->currentPage(),
                        'last_page' => $clients->lastPage(),
                        'per_page' => $clients->perPage(),
                        'total' => $clients->total(),
                    ],
                ],
                'stats' => $stats,
                'filters' => $filters,
            ];

        } catch (\Exception $e) {
            throw new \Exception('Erreurs lors du chargement des données.');
        }

    }
}
