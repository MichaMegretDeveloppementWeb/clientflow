<?php

namespace App\Repositories\Contracts\Clients;

use App\Models\Client;

interface ClientEditRepositoryInterface
{
    /**
     * Find a client for editing with security check
     */
    public function findClientForEdit(int $clientId): ?Client;
}
