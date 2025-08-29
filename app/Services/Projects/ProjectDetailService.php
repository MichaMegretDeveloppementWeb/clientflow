<?php

namespace App\Services\Projects;

use App\Models\Project;
use App\Repositories\Contracts\Projects\ProjectDetailRepositoryInterface;
use App\DTOs\ProjectDTO;

class ProjectDetailService
{
    public function __construct(
        private readonly ProjectDetailRepositoryInterface $projectRepository,
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
                'status' => '',
                'description' => '',
                'budget' => null,
                'start_date' => null,
                'end_date' => null,
                'created_at' => null,
                'updated_at' => null,
                'status_label' => '',
                'budget_formatted' => '',
                'is_overdue' => false,
                'client' => [
                    'id' => null,
                    'name' => ''
                ]
            ],
            'events' => []
        ];
    }

    /**
     * Get complete project detail data for AJAX response - IDENTIQUE à getProjectDetails()
     */
    public function getCompleteData(int $projectId): array
    {
        try {
            return $this->getProjectDetails($projectId);
        }catch (\Exception $e) {
            throw new \Exception("Erreurs lors du chargement des données.");
        }
    }

    /**
     * Get project details - COPIE EXACTE de ProjectService::getProjectDetails()
     */
    public function getProjectDetails(int $projectId): array
    {

        $project = $this->projectRepository->findWithRelations($projectId, ['client', 'events']);

        if (!$project) {
            return [
                'project' => [],
                'errors' => [
                    'project' => 'Projet introuvable',
                ]
            ];
        }

        return [
            'project' => ProjectDTO::fromModel($project)->toDetailArray(),
            'events' => $project->events->map(function ($event) {
                return [
                    'id' => $event->id,
                    'name' => $event->name,
                    'description' => $event->description,
                    'type' => $event->type,
                    'event_type' => $event->event_type,
                    'status' => $event->status,
                    'amount' => $event->amount,
                    'payment_status' => $event->payment_status,
                    'execution_date' => $event->execution_date?->toISOString(),
                    'send_date' => $event->send_date?->toISOString(),
                    'payment_due_date' => $event->payment_due_date?->toISOString(),
                    'completed_at' => $event->completed_at?->toISOString(),
                    'paid_at' => $event->paid_at?->toISOString(),
                    'created_at' => $event->created_at->toISOString(),
                    'updated_at' => $event->updated_at->toISOString(),
                ];
            }),
            'errors' => []
        ];
    }
}
