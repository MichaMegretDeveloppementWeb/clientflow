<?php

namespace App\Repositories\Events;

use App\Models\Event;
use App\Repositories\Contracts\Events\EventUpdateRepositoryInterface;

class EventUpdateRepository implements EventUpdateRepositoryInterface
{
    /**
     * Update an event with the given data
     */
    public function update(Event $event, array $data): Event
    {
        $event->update($data);
        return $event->fresh();
    }
}