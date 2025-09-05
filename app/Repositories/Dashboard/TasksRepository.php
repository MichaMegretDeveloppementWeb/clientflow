<?php

namespace App\Repositories\Dashboard;

use App\Enums\EventStatus;
use App\Enums\EventType;
use App\Models\Event;
use App\Repositories\Contracts\Dashboard\TasksRepositoryInterface;
use Illuminate\Support\Collection;

class TasksRepository implements TasksRepositoryInterface
{
    /**
     * Get upcoming tasks with optional urgency filter
     * OPTIMIZED: Direct JOIN queries with minimal data selection
     */
    public function getUpcomingTasks(int $userId, bool $urgentOnly = false, int $limit = 200): Collection
    {
        $query = Event::select([
            'events.id', 'events.name', 'events.description', 'events.event_type',
            'events.status', 'events.execution_date', 'events.send_date', 'events.amount',
            'projects.id as project_id', 'projects.name as project_name',
            'clients.id as client_id', 'clients.name as client_name',
        ])
            ->join('projects', 'events.project_id', '=', 'projects.id')
            ->join('clients', 'projects.client_id', '=', 'clients.id')
            ->where('clients.user_id', $userId)
            ->where(function ($query) use ($urgentOnly) {
                if ($urgentOnly) {
                    // Tâches urgentes (en retard)
                    $query->where(function ($q) {
                        $q->where('events.event_type', EventType::Step->value)
                            ->where('events.status', EventStatus::Todo->value)
                            ->whereDate('events.execution_date', '<', now());
                    })
                        ->orWhere(function ($q) {
                            $q->where('events.event_type', EventType::Billing->value)
                                ->where('events.status', EventStatus::ToSend->value)
                                ->whereDate('events.send_date', '<', now());
                        });
                } else {
                    // Toutes les tâches à venir (non terminées)
                    $query->where(function ($q) {
                        $q->where('events.event_type', EventType::Step->value)
                            ->where('events.status', EventStatus::Todo->value);
                    })
                        ->orWhere(function ($q) {
                            $q->where('events.event_type', EventType::Billing->value)
                                ->where('events.status', EventStatus::ToSend->value);
                        });
                }
            })
            ->orderByRaw('
                CASE
                    WHEN events.event_type = ? THEN events.execution_date
                    WHEN events.event_type = ? THEN events.send_date
                END ASC
            ', [EventType::Step->value, EventType::Billing->value])
            ->limit($limit);

        return $query->get()
            ->map(function ($event) {
                // Transform to match expected structure
                $event->project = (object) [
                    'id' => $event->project_id,
                    'name' => $event->project_name,
                    'client' => (object) [
                        'id' => $event->client_id,
                        'name' => $event->client_name,
                    ],
                ];

                return $event;
            });
    }

    /**
     * Get urgent tasks count
     * OPTIMIZED: Count only query for performance
     */
    public function getUrgentTasksCount(int $userId): int
    {
        return Event::join('projects', 'events.project_id', '=', 'projects.id')
            ->join('clients', 'projects.client_id', '=', 'clients.id')
            ->where('clients.user_id', $userId)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('events.event_type', EventType::Step->value)
                        ->where('events.status', EventStatus::Todo->value)
                        ->whereDate('events.execution_date', '<', now());
                })
                    ->orWhere(function ($q) {
                        $q->where('events.event_type', EventType::Billing->value)
                            ->where('events.status', EventStatus::ToSend->value)
                            ->whereDate('events.send_date', '<', now());
                    });
            })
            ->count();
    }
}
