<?php

namespace App\Repositories\Contracts\Events;

use App\Models\Event;

interface EventEditRepositoryInterface
{
    public function findEventForEdit(int $eventId): ?Event;
}