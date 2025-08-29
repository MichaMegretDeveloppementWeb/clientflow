<?php

namespace App\Repositories\Contracts\Clients;

use App\Models\Client;

interface ClientUpdateRepositoryInterface
{
    /**
     * Update an existing client
     */
    public function update(Client $client, array $data): Client;
}
