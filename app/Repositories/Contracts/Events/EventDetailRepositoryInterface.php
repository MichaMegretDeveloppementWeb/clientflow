<?php

namespace App\Repositories\Contracts\Events;

use App\Models\Event;

interface EventDetailRepositoryInterface
{
    public function findWithRelations(int $eventId, array $relations = []): ?Event;
}
