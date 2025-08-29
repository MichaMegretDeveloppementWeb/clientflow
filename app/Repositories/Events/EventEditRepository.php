<?php

namespace App\Repositories\Events;

use App\Models\Event;
use App\Repositories\Contracts\Events\EventEditRepositoryInterface;

class EventEditRepository implements EventEditRepositoryInterface
{
    /**
     * Find an event for editing with security check and required relations
     */
    public function findEventForEdit(int $eventId): ?Event
    {
        return Event::whereHas('project.client', function($q) {
            $q->where('clients.user_id', auth()->id());
        })
        ->with(['project.client'])
        ->find($eventId);
    }
}