<?php

namespace App\Services\Events;

use App\Repositories\Contracts\Events\EventListRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class EventListService
{
    public function __construct(
        private readonly EventListRepositoryInterface $eventRepository,
    ) {}

    /**
     * Get paginated events with filters
     */
    public function getPaginatedEvents(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->eventRepository->paginate($perPage, $filters);
    }

    /**
     * Get events statistics (suivant le pattern des autres services)
     */
    public function getEventsStatistics(array $filters = []): array
    {
        return $this->eventRepository->getGlobalStatistics();
    }

    /**
     * Get available projects for filters
     */
    public function getAvailableProjects(): array
    {
        return $this->eventRepository->getAvailableProjects();
    }

    /**
     * Get available clients for filters
     */
    public function getAvailableClients(): array
    {
        return $this->eventRepository->getAvailableClients();
    }

    /**
     * Get skeleton data structure
     */
    public function getSkeletonData(): array
    {
        return [
            'events' => [
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 15,
                    'total' => 0,
                ],
            ],
            'stats' => [
                'total' => 0,
                'todo' => 0,
                'done' => 0,
                'overdue' => 0,
            ],
            'projects' => [],
            'clients' => [],
        ];
    }

    /**
     * Get complete data for AJAX response
     * OPTIMIZED: Direct model usage, no DTOs to avoid N+1 queries
     */
    public function getCompleteData(array $filters = [], int $perPage = 15): array
    {
        try {
            $events = $this->getPaginatedEvents($filters, $perPage);
            $stats = $this->getEventsStatistics($filters);

            // Transform events data to match frontend expectations
            // without DTOs to avoid N+1 queries - convert to array to avoid model accessors
            $eventsData = $events->getCollection()->map(function ($event) {
                return $this->transformEventForFrontend($event);
            })->toArray();

            return [
                'events' => [
                    'data' => $eventsData,
                    'meta' => [
                        'current_page' => $events->currentPage(),
                        'last_page' => $events->lastPage(),
                        'per_page' => $events->perPage(),
                        'total' => $events->total(),
                    ],
                ],
                'stats' => $stats,
                'projects' => $this->getAvailableProjects(),
                'clients' => $this->getAvailableClients(),
                'filters' => $filters,
            ];

        } catch (\Exception $e) {
            throw new \Exception('Erreurs lors du chargement des données.');
        }
    }

    /**
     * Transform Event model to match frontend EventDTO expectations
     * without creating DTO objects (performance optimization)
     */
    private function transformEventForFrontend($event): array
    {
        // Calculate labels directly (no enum lookup needed)
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

        // Calculate is_overdue (replicate model logic)
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

        // Calculate is_payment_overdue (replicate model logic)
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
            'created_date' => $event->created_date->format('Y-m-d'),
            'execution_date' => $event->execution_date?->format('Y-m-d'),
            'send_date' => $event->send_date?->format('Y-m-d'),
            'payment_due_date' => $event->payment_due_date?->format('Y-m-d'),
            'completed_at' => $event->completed_at?->format('Y-m-d H:i:s'),
            'paid_at' => $event->paid_at?->format('Y-m-d'),
            'created_at' => $event->created_at?->toISOString(),
            'updated_at' => $event->updated_at?->toISOString(),

            // Labels calculés
            'event_type_label' => $eventTypeLabels[$event->event_type] ?? $event->event_type,
            'status_label' => $statusLabels[$event->status] ?? $event->status,
            'payment_status_label' => $event->event_type === 'billing'
                ? ($paymentStatusLabels[$event->payment_status] ?? $event->payment_status)
                : null,

            // Calculs booléens
            'is_overdue' => $isOverdue,
            'is_payment_overdue' => $isPaymentOverdue,

            // Montant formaté
            'formatted_amount' => $formattedAmount,

            // Relations (déjà chargées via with())
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
