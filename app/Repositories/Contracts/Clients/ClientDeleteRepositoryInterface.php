<?php

namespace App\Repositories\Contracts\Clients;

use App\Models\Client;

interface ClientDeleteRepositoryInterface
{
    /**
     * Delete a client (soft delete)
     */
    public function delete(Client $client): bool;
}
