<?php

namespace App\Services\Projects;

use App\Repositories\Contracts\Projects\ProjectCreateRepositoryInterface;

class ProjectCreateService
{
    public function __construct(
        private readonly ProjectCreateRepositoryInterface $projectCreateRepository,
    ) {}

    /**
     * Get skeleton data structure for loading states
     */
    public function getSkeletonData(): array
    {
        return [
            'clients' => [],
            'selectedClientId' => null,
        ];
    }

    /**
     * Get complete data for project creation
     */
    public function getCompleteData(?int $selectedClientId = null): array
    {
        try {

            return [
                'clients' => $this->projectCreateRepository->getAvailableClients(),
                'selectedClientId' => $selectedClientId,
            ];

        }catch (\Exception $e) {
            throw new \Exception("Erreurs lors du chargement des données.");
        }
    }
}
