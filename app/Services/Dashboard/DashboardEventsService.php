<?php

namespace App\Services\Dashboard;

use App\Models\Event;
use App\Enums\EventStatus;
use App\Enums\EventType;
use Illuminate\Support\Collection;

class DashboardEventsService
{
    /**
     * Get upcoming tasks with optional urgency filter
     */
    public function getUpcomingTasks(bool $urgentOnly = false, int $limit = 100): Collection
    {
        $userId = auth()->id();

        $query = Event::with(['project.client'])
            ->whereHas('project.client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where(function ($query) use ($urgentOnly) {
                if ($urgentOnly) {
                    // Tâches urgentes (en retard)
                    $query->where(function ($q) {
                        $q->where('event_type', EventType::Step->value)
                            ->where('status', EventStatus::Todo->value)
                            ->whereDate('execution_date', '<', now());
                    })
                    ->orWhere(function ($q) {
                        $q->where('event_type', EventType::Billing->value)
                            ->where('status', EventStatus::ToSend->value)
                            ->whereDate('send_date', '<', now());
                    });
                } else {
                    // Toutes les tâches à venir (non terminées)
                    $query->where(function ($q) {
                        $q->where('event_type', EventType::Step->value)
                            ->where('status', EventStatus::Todo->value);
                    })
                    ->orWhere(function ($q) {
                        $q->where('event_type', EventType::Billing->value)
                            ->where('status', EventStatus::ToSend->value);
                    });
                }
            })
            ->orderByRaw('
                CASE
                    WHEN event_type = ? THEN execution_date
                    WHEN event_type = ? THEN send_date
                END ASC
            ', [EventType::Step->value, EventType::Billing->value]);

        return $query->get();
    }

    /**
     * Get urgent tasks (overdue events) - kept for backward compatibility
     */
    public function getUrgentTasks(int $limit = 10): Collection
    {
        return $this->getUpcomingTasks(true, $limit);
    }

    /**
     * Get upcoming events
     */
    public function getUpcomingEvents(int $limit = 10): Collection
    {
        $userId = auth()->id();
        return Event::with(['project.client'])
            ->whereHas('project.client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->upcoming()
            ->limit($limit)
            ->get();
    }

}
