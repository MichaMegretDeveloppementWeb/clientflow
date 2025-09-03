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
     * Get project details with financial statistics
     */
    public function getProjectDetails(int $projectId): array
    {
        $project = $this->projectRepository->findWithFinancialStats($projectId);

        if (! $project) {
            return [
                'project' => [],
                'events' => [],
                'errors' => [
                    'project' => 'Projet introuvable',
                ],
            ];
        }

        return [
            'project' => $this->transformProjectForDetail($project),
            'financialStats' => $this->extractFinancialStats($project),
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
     * Transform Project model for detail page with financial stats
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

        // Calculate budget exceeded
        $isBudgetExceeded = $project->budget &&
                           (float) $project->total_billed > (float) $project->budget;

        // Calculate remaining budget
        $remainingBudget = $project->budget ?
                          (float) $project->budget - (float) $project->total_billed :
                          null;

        // Calculate budget usage percentage
        $budgetUsagePercentage = $project->budget && $project->budget > 0 ?
                                min(((float) $project->total_billed / (float) $project->budget) * 100, 100) :
                                0;

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

            // Calculs booléens
            'is_overdue' => $isOverdue,
            'has_overdue_events' => (bool) $project->has_overdue_events,
            'has_overdue_payments' => (bool) $project->has_overdue_payments,

            // Relations (déjà chargées)
            'client' => $project->client ? [
                'id' => $project->client->id,
                'name' => $project->client->name,
                'company' => $project->client->company,
                'email' => $project->client->email,
            ] : null,
        ];
    }

    /**
     * Extract financial statistics from project (raw amounts, no formatting)
     */
    private function extractFinancialStats($project): array
    {
        // Calculate budget exceeded
        $isBudgetExceeded = $project->budget && 
                           (float) $project->total_billed > (float) $project->budget;

        // Calculate remaining budget
        $remainingBudget = $project->budget ? 
                          (float) $project->budget - (float) $project->total_billed : 
                          null;

        // Calculate budget usage percentage
        $budgetUsagePercentage = $project->budget && $project->budget > 0 ? 
                                min(((float) $project->total_billed / (float) $project->budget) * 100, 100) : 
                                0;

        return [
            'budget' => (float) $project->budget,
            'totalBilled' => (float) $project->total_billed,
            'totalPaid' => (float) $project->total_paid,
            'totalUnpaid' => (float) $project->total_unpaid,
            'billsToSend' => (float) $project->bills_to_send,
            'upcomingPayments' => (float) $project->upcoming_payments,
            'overdueUnpaid' => (float) $project->overdue_unpaid,
            'remainingBudget' => $remainingBudget,
            'budgetUsage' => [
                'percentage' => round($budgetUsagePercentage),
                'isExceeded' => $isBudgetExceeded,
            ],
        ];
    }
}
