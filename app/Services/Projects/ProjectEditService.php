<?php

namespace App\Services\Projects;

use App\Repositories\Contracts\Projects\ProjectEditRepositoryInterface;
use App\DTOs\ProjectDTO;
use App\Models\Project;

class ProjectEditService
{
    public function __construct(
        private readonly ProjectEditRepositoryInterface $projectRepository,
    ) {}

    /**
     * Get skeleton data structure for loading states
     */
    public function getSkeletonData(): array
    {
        return [
            'project' => [
                'id' => null,
                'name' => '',
                'description' => '',
                'status' => 'active',
                'budget' => null,
                'start_date' => '',
                'end_date' => '',
                'client_id' => null,
                'created_at' => '',
                'updated_at' => '',
                'client' => [
                    'id' => null,
                    'name' => '',
                    'company' => ''
                ]
            ]
        ];
    }

    /**
     * Get complete project data for editing
     */
    public function getCompleteData(int $projectId): array
    {
        try {

            $project = $this->projectRepository->findProjectForEdit($projectId);

            if (!$project) {
                return [
                    'project' => [],
                    'errors' => [
                        'project' => 'Projet introuvable ou accès non autorisé',
                    ]
                ];
            }

            return [
                'project' => ProjectDTO::fromModel($project)->toEditArray(),
                'errors' => []
            ];

        }catch (\Exception $e) {
            throw new \Exception("Erreurs lors du chargement des données.");
        }

    }

    /**
     * Get project details for editing - Alias for getCompleteData
     */
    public function getProjectForEdit(int $projectId): array
    {
        return $this->getCompleteData($projectId);
    }
}
