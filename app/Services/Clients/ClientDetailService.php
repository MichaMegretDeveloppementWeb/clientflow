<?php

namespace App\Services\Clients;

use App\Repositories\Contracts\Clients\ClientDetailRepositoryInterface;

class ClientDetailService
{
    public function __construct(
        private readonly ClientDetailRepositoryInterface $clientDetailRepository
    ) {}


    /**
     * Get skeleton data structure
     */
    public function getSkeletonData(int $clientId): array
    {

        return [
            'client' => [
                'id' => $clientId,
                'name' => '',
                'company' => null,
                'email' => null,
                'phone' => null,
                'address' => null,
                'notes' => null,
                'project_stats' => [
                    'total_projects' => 0,
                    'active_projects' => 0,
                    'completed_projects' => 0,
                    'on_hold_projects' => 0,
                ]
            ],
            'projects' => [],
            'events' => [],
            'financialStats' => [
                'total_billed' => 0,
                'total_paid' => 0,
                'total_pending' => 0,
                'total_upcoming_payment' => 0,
                'total_overdue_payment' => 0,
            ],
            'error' => null
        ];
    }



    /**
     * Get complete data for detail view (compatible with Show.vue format)
     */
    public function getCompleteData(int $clientId): array
    {
        try {
            // Pour récupérer juste le client de base sans les relations
            $client = $this->clientDetailRepository->find($clientId);

            return [
                'client' => $this->enhanceClientData($client),
                'projects' => $this->formatClientProjects($clientId),
                'events' => $this->formatClientEvents($clientId, []),
                'financialStats' => $this->formatFinancialStats($clientId),
                'error' => null
            ];
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return [
                'client' => null,
                'projects' => [],
                'events' => [],
                'financialStats' => null,
                'error' => [
                    'type' => 'access_denied',
                    'title' => 'Client introuvable',
                    'message' => 'Le client sélectionné n\'existe pas.',
                    'code' => 403
                ]
            ];
        } catch (\Exception $e) {
            return [
                'client' => null,
                'projects' => [],
                'events' => [],
                'financialStats' => null,
                'error' => [
                    'type' => 'general_error',
                    'title' => 'Erreur inattendue',
                    'message' => 'Une erreur est survenue lors du chargement des données.',
                    'code' => 500
                ]
            ];
        }
    }


    /**
     * Enhance client data with computed fields including project stats
     */
    private function enhanceClientData($client): array
    {
        return [
            'id' => $client->id,
            'name' => $client->name,
            'company' => $client->company,
            'email' => $client->email,
            'phone' => $client->phone,
            'address' => $client->address,
            'notes' => $client->notes,
            'created_at' => $client->created_at?->toISOString(),
            'updated_at' => $client->updated_at?->toISOString(),
            'project_stats' => $this->getProjectStats($client->id),
        ];
    }

    /**
     * Get project statistics for client
     */
    private function getProjectStats(int $clientId): array
    {
        $stats = $this->clientDetailRepository->getProjectStats($clientId);

        return [
            'total_projects' => $stats['total_projects'] ?? 0,
            'active_projects' => $stats['active_projects'] ?? 0,
            'completed_projects' => $stats['completed_projects'] ?? 0,
            'on_hold_projects' => $stats['on_hold_projects'] ?? 0,
        ];
    }


    /**
     * Format client projects from repository data
     */
    private function formatClientProjects(int $clientId): array
    {
        $projects = $this->clientDetailRepository->getClientProjects($clientId);

        return $projects->map(function ($project) {
            return [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'status' => $project->status,
                'budget' => $project->budget,
                'start_date' => $project->start_date,
                'end_date' => $project->end_date,
                'events_count' => $project->project_events_count ?? 0, // Utilise withCount optimisé
                'billing_total' => $project->project_billing_total ?? 0, // Montant total facturé
                'has_overdue_events' => $project->has_overdue_events ?? false, // Événements en retard
                'has_payment_overdue' => $project->has_payment_overdue ?? false, // Paiements en retard
                'created_at' => $project->created_at?->toISOString(),
            ];
        })->toArray();
    }



    /**
     * Format client events from repository data
     */
    private function formatClientEvents(int $clientId, array $filters): array
    {
        $events = $this->clientDetailRepository->getFilteredClientEvents($clientId, $filters);

        return $events->map(function ($event) {
            return [
                'id' => $event->id,
                'name' => $event->name,
                'description' => $event->description,
                'event_type' => $event->event_type,
                'event_type_label' => $event->event_type === 'billing' ? 'Facturation' : 'Étape',
                'status' => $event->status,
                'status_label' => $this->getStatusLabel($event->status),
                'amount' => $event->amount,
                'payment_status' => $event->payment_status,
                'payment_status_label' => $event->payment_status,
                'due_date' => $event->due_date,
                'execution_date' => $event->execution_date,
                'send_date' => $event->send_date,
                'payment_due_date' => $event->payment_due_date,
                'paid_at' => $event->paid_at,
                'is_overdue' => $event->is_overdue ?? false,
                'is_payment_overdue' => $event->is_payment_overdue ?? false,
                'days_overdue' => $event->days_overdue ?? 0,
                'project' => [
                    'id' => $event->project_id,
                    'name' => $event->project_name
                ]
            ];
        })->toArray();
    }



    /**
     * Format financial statistics from repository data
     */
    private function formatFinancialStats(int $clientId): array
    {
        $stats = $this->clientDetailRepository->getFinancialStats($clientId);

        return [
            'total_billed' => (float) $stats['total_billed'],
            'total_paid' => (float) $stats['total_paid'],
            'total_pending' => (float) $stats['total_pending'],
            'total_sent' => (float) $stats['total_sent'],
            'total_upcoming_payment' => (float) $stats['total_upcoming_payment'],
            'total_overdue_payment' => (float) $stats['total_overdue_payment'],
        ];
    }

    /**
     * Get status label
     */
    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'todo' => 'À faire',
            'done' => 'Terminé',
            'to_send' => 'À envoyer',
            'sent' => 'Envoyé',
            'cancelled' => 'Annulé',
            default => $status
        };
    }
}
