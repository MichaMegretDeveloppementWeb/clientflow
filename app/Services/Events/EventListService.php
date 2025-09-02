<?php

namespace App\Services\Events;

use App\Repositories\Contracts\Events\EventListRepositoryInterface;
use App\DTOs\EventDTO;
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
                    'total' => 0
                ]
            ],
            'stats' => [
                'total' => 0,
                'todo' => 0,
                'done' => 0,
                'overdue' => 0,
                'step_events' => 0,
                'billing_events' => 0
            ],
            'projects' => [],
            'clients' => []
        ];
    }

    /**
     * Get complete data for AJAX response
     */
    public function getCompleteData(array $filters = [], int $perPage = 15): array
    {

        try {

            $events = $this->getPaginatedEvents($filters, $perPage);
            $stats = $this->getEventsStatistics($filters);

            return [
                'events' => [
                    'data' => EventDTO::collection($events->getCollection())->map->toArray(),
                    'meta' => [
                        'current_page' => $events->currentPage(),
                        'last_page' => $events->lastPage(),
                        'per_page' => $events->perPage(),
                        'total' => $events->total()
                    ]
                ],
                'stats' => $stats,
                'projects' => $this->getAvailableProjects(),
                'clients' => $this->getAvailableClients(),
                'filters' => $filters,
            ];

        }catch (\Exception $e) {
            throw new \Exception("Erreurs lors du chargement des données.");
        }

    }
}
