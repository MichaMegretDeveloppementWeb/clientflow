<?php

namespace App\Services\Events;

use App\Repositories\Contracts\Events\EventEditRepositoryInterface;
use App\DTOs\EventDTO;
use App\Models\Event;

class EventEditService
{
    public function __construct(
        private readonly EventEditRepositoryInterface $eventRepository,
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
                'project' => [
                    'id' => null,
                    'name' => '',
                    'start_date' => '',
                    'client' => [
                        'id' => null,
                        'name' => '',
                    ]
                ]
            ]
        ];
    }

    /**
     * Get complete event data for editing
     */
    public function getCompleteData(int $eventId): array
    {
        try {

            $event = $this->eventRepository->findEventForEdit($eventId);

            if (!$event) {
                return [
                    'event' => [],
                    'errors' => [
                        'event' => 'Événement introuvable ou accès non autorisé',
                    ]
                ];
            }

            return [
                'event' => EventDTO::fromModel($event)->toEditArray(),
                'errors' => []
            ];

        }catch (\Exception $e) {
            throw new \Exception("Erreurs lors du chargement des données.");
        }

    }

    /**
     * Get event details for editing - Alias for getCompleteData
     */
    public function getEventForEdit(int $eventId): array
    {
        return $this->getCompleteData($eventId);
    }
}
