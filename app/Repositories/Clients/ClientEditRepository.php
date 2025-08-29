<?php

namespace App\Repositories\Clients;

use App\Models\Client;
use App\Repositories\Contracts\Clients\ClientEditRepositoryInterface;

class ClientEditRepository implements ClientEditRepositoryInterface
{
    /**
     * Find a client for editing with security check
     */
    public function findClientForEdit(int $clientId): ?Client
    {
        return Client::where('user_id', auth()->id())
            ->find($clientId);
    }
}
