<?php

namespace App\Repositories\Clients;

use App\Models\Client;
use App\Repositories\Contracts\Clients\ClientUpdateRepositoryInterface;

class ClientUpdateRepository implements ClientUpdateRepositoryInterface
{
    /**
     * Update an existing client
     */
    public function update(Client $client, array $data): Client
    {
        $client->update($data);
        return $client->fresh();
    }
}
