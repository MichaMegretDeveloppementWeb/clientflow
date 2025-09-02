<?php

namespace App\Services\Events;

use App\Repositories\Contracts\Events\EventCreateRepositoryInterface;

class EventCreateService
{
    public function __construct(
        private readonly EventCreateRepositoryInterface $eventCreateRepository,
    ) {}

    /**
     * Get skeleton data structure for loading states
     */
    public function getSkeletonData(): array
    {
        return [
            'projects' => [],
            'selectedProject' => null,
            'event' => [
                'id' => null,
                'project_id' => null,
                'name' => '',
                'description' => '',
                'type' => '',
                'event_type' => 'step',
                'status' => 'todo',
                'created_date' => '',
                'execution_date' => '',
                'send_date' => '',
                'payment_due_date' => '',
                'completed_at' => '',
                'amount' => null,
                'payment_status' => 'pending',
                'paid_at' => '',
            ],
            'errors' => []
        ];
    }

    /**
     * Get complete data for event creation
     */
    public function getCompleteData(?int $projectId = null): array
    {
        try {

            $projects = $this->eventCreateRepository->getAllProjects();
            $selectedProject = null;

            if ($projectId) {
                $selectedProject = $this->eventCreateRepository->getProjectById($projectId);
            }

            return [
                'projects' => $projects,
                'selectedProject' => $selectedProject,
                'event' => [
                    'id' => null,
                    'project_id' => $projectId,
                    'name' => '',
                    'description' => '',
                    'type' => '',
                    'event_type' => 'step',
                    'status' => 'todo',
                    'created_date' => now()->format('Y-m-d'),
                    'execution_date' => '',
                    'send_date' => '',
                    'payment_due_date' => '',
                    'completed_at' => '',
                    'amount' => null,
                    'payment_status' => 'pending',
                    'paid_at' => '',
                ],
                'errors' => []
            ];

        }catch (\Exception $e) {
            throw new \Exception("Erreurs lors du chargement des données.");
        }

    }
}
