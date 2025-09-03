<?php

namespace App\Services\Events;

use App\Models\Event;
use App\Repositories\Contracts\Events\EventDetailRepositoryInterface;

class EventDetailService
{
    public function __construct(
        private readonly EventDetailRepositoryInterface $eventRepository,
    ) {}

    /**
     * Get skeleton data structure for loading states
     */
    public function getSkeletonData(): array
    {
        return [
            'event' => [
                'id' => null,
                'name' => '',
                'status' => '',
                'event_type' => '',
                'description' => '',
                'type' => '',
                'amount' => null,
                'execution_date' => null,
                'send_date' => null,
                'payment_due_date' => null,
                'completed_at' => null,
                'paid_at' => null,
                'created_date' => null,
                'updated_at' => null,
                'status_label' => '',
                'event_type_label' => '',
                'payment_status' => '',
                'payment_status_label' => '',
                'is_overdue' => false,
                'is_payment_overdue' => false,
                'project' => [
                    'id' => null,
                    'name' => '',
                    'client' => [
                        'id' => null,
                        'name' => '',
                    ],
                ],
            ],
        ];
    }

    /**
     * Get complete event detail data for AJAX response - IDENTIQUE à getEventDetails()
     */
    public function getCompleteData(int $eventId): array
    {
        return $this->getEventDetails($eventId);
    }

    /**
     * Get event details - COPIE EXACTE de EventService::getEventDetails()
     */
    public function getEventDetails(int $eventId): array
    {

        $event = $this->eventRepository->findWithRelations($eventId, ['project.client']);

        if (! $event) {
            return [
                'event' => [],
                'errors' => [
                    'event' => 'Evenement introuvable',
                ],
            ];
        }

        return [
            'event' => $this->transformEventForDetail($event),
            'errors' => [],
        ];
    }

    /**
     * Transform Event model for detail page without DTO
     */
    private function transformEventForDetail($event): array
    {
        // Utiliser la même logique que dans EventListService mais pour le détail
        $eventTypeLabels = [
            'step' => 'Étape',
            'billing' => 'Facturation',
        ];

        $statusLabels = [
            'todo' => 'À faire',
            'done' => 'Fait',
            'to_send' => 'À envoyer',
            'sent' => 'Envoyé',
            'cancelled' => 'Annulé',
        ];

        $paymentStatusLabels = [
            'paid' => 'Payé',
            'pending' => 'En attente',
        ];

        // Calculate is_overdue
        $isOverdue = false;
        if ($event->event_type === 'step') {
            $isOverdue = $event->status === 'todo' &&
                        $event->execution_date &&
                        $event->execution_date->startOfDay()->lt(now()->startOfDay());
        } elseif ($event->event_type === 'billing') {
            $isOverdue = $event->status === 'to_send' &&
                        $event->send_date &&
                        $event->send_date->startOfDay()->lt(now()->startOfDay());
        }

        // Calculate is_payment_overdue
        $isPaymentOverdue = $event->event_type === 'billing' &&
                           $event->payment_status === 'pending' &&
                           $event->payment_due_date &&
                           $event->payment_due_date->startOfDay()->lt(now()->startOfDay());

        // Format amount
        $formattedAmount = null;
        if ($event->event_type === 'billing' && $event->amount) {
            $formattedAmount = number_format($event->amount, 2, ',', ' ').' €';
        }

        return [
            'id' => $event->id,
            'project_id' => $event->project_id,
            'name' => $event->name,
            'description' => $event->description,
            'type' => $event->type,
            'event_type' => $event->event_type,
            'status' => $event->status,
            'amount' => $event->amount,
            'payment_status' => $event->payment_status,
            'created_date' => $event->created_date,
            'execution_date' => $event->execution_date,
            'send_date' => $event->send_date,
            'payment_due_date' => $event->payment_due_date,
            'completed_at' => $event->completed_at,
            'paid_at' => $event->paid_at,
            'created_at' => $event->created_at?->toISOString(),
            'updated_at' => $event->updated_at?->toISOString(),

            // Labels calculés
            'event_type_label' => $eventTypeLabels[$event->event_type] ?? $event->event_type,
            'status_label' => $statusLabels[$event->status] ?? $event->status,
            'payment_status_label' => $event->event_type === 'billing'
                ? ($paymentStatusLabels[$event->payment_status] ?? $event->payment_status)
                : null,
            'formatted_amount' => $formattedAmount,

            // Calculs booléens
            'is_overdue' => $isOverdue,
            'is_payment_overdue' => $isPaymentOverdue,
            'is_todo' => in_array($event->status, ['todo', 'to_send']),

            // Relations (déjà chargées)
            'project' => $event->project ? [
                'id' => $event->project->id,
                'name' => $event->project->name,
                'client' => $event->project->client ? [
                    'id' => $event->project->client->id,
                    'name' => $event->project->client->name,
                    'company' => $event->project->client->company,
                ] : null,
            ] : null,
        ];
    }
}
