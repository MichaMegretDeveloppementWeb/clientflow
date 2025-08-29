<?php

namespace App\Repositories\Contracts\Events;

use App\Models\Event;

interface EventUpdateRepositoryInterface
{
    public function update(Event $event, array $data): Event;
}