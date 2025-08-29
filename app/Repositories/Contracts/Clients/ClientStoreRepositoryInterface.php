<?php

namespace App\Repositories\Contracts\Clients;

use App\Models\Client;

interface ClientStoreRepositoryInterface
{
    /**
     * Create a new client
     */
    public function create(array $data): Client;

    /**
     * Check if email exists for user
     */
    public function emailExistsForUser(string $email, int $userId): bool;
}
