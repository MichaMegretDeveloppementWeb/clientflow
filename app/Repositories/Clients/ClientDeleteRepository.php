<?php

namespace App\Repositories\Clients;

use App\Models\Client;
use App\Repositories\Contracts\Clients\ClientDeleteRepositoryInterface;

class ClientDeleteRepository implements ClientDeleteRepositoryInterface
{
    /**
     * Delete a client (soft delete)
     */
    public function delete(Client $client): bool
    {
        return $client->delete();
    }
}
