<?php

namespace App\Services\Projects;

use App\Models\Project;
use App\Repositories\Contracts\Projects\ProjectDetailRepositoryInterface;

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
                    'name' => '',
                ],
            ],
            'events' => [],
        ];
    }

    /**
     * Get complete project detail data for AJAX response - IDENTIQUE à getProjectDetails()
     */
    public function getCompleteData(int $projectId): array
    {
        try {
            return $this->getProjectDetails($projectId);
        } catch (\Exception $e) {
            throw new \Exception('Erreurs lors du chargement des données.');
        }
    }

    /**
     * Get project details - COPIE EXACTE de ProjectService::getProjectDetails()
     */
    public function getProjectDetails(int $projectId): array
    {

        $project = $this->projectRepository->findWithRelations($projectId, ['client', 'events']);

        if (! $project) {
            return [
                'project' => [],
                'errors' => [
                    'project' => 'Projet introuvable',
                ],
            ];
        }

        return [
            'project' => $this->transformProjectForDetail($project),
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
                    'created_date' => $event->created_date->toISOString(),
                    'updated_at' => $event->updated_at->toISOString(),
                ];
            }),
            'errors' => [],
        ];
    }

    /**
     * Transform Project model for detail page without DTO
     */
    private function transformProjectForDetail($project): array
    {
        $statusLabels = [
            'active' => 'Actif',
            'completed' => 'Terminé',
            'on_hold' => 'En pause',
            'cancelled' => 'Annulé',
        ];

        // Calculate is_overdue for project
        $isOverdue = $project->status === 'active' &&
                    $project->end_date &&
                    $project->end_date->startOfDay()->lt(now()->startOfDay());

        return [
            'id' => $project->id,
            'client_id' => $project->client_id,
            'name' => $project->name,
            'description' => $project->description,
            'status' => $project->status,
            'budget' => $project->budget,
            'start_date' => $project->start_date?->toISOString(),
            'end_date' => $project->end_date?->toISOString(),
            'created_at' => $project->created_at?->toISOString(),
            'updated_at' => $project->updated_at?->toISOString(),

            // Labels calculés
            'status_label' => $statusLabels[$project->status] ?? $project->status,
            'budget_formatted' => $project->budget ? number_format($project->budget, 2, ',', ' ').' €' : null,

            // Calculs booléens
            'is_overdue' => $isOverdue,

            // Relations (déjà chargées)
            'client' => $project->client ? [
                'id' => $project->client->id,
                'name' => $project->client->name,
                'company' => $project->client->company,
                'email' => $project->client->email,
            ] : null,
        ];
    }
}
