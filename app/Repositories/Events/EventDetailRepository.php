<?php

namespace App\Repositories\Events;

use App\Models\Event;
use App\Repositories\Contracts\Events\EventDetailRepositoryInterface;

class EventDetailRepository implements EventDetailRepositoryInterface
{
    public function findWithRelations(int $eventId, array $relations = []): ?Event
    {
        $defaultRelations = ['project.client'];
        $relations = array_merge($defaultRelations, $relations);

        return Event::whereHas('project.client', function($q) {
            $q->where('clients.user_id', auth()->id());
        })
        ->with($relations)
        ->find($eventId);
    }
}
