<?php

namespace App\Services\Events;

use App\Repositories\Contracts\Events\EventDetailRepositoryInterface;
use App\DTOs\EventDTO;
use App\Models\Event;

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
                        'name' => ''
                    ]
                ]
            ]
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

        if (!$event) {
            return [
                'event' => [],
                'errors' => [
                    'event' => 'Evenement introuvable',
                ]
            ];
        }

        return [
            'event' => EventDTO::fromModel($event)->toDetailArray(),
            'errors' => []
        ];
    }
}
