<?php

namespace App\Repositories\Clients;

use App\Models\Client;
use App\Repositories\Contracts\Clients\ClientStoreRepositoryInterface;

class ClientStoreRepository implements ClientStoreRepositoryInterface
{
    /**
     * Create a new client
     */
    public function create(array $data): Client
    {
        return Client::create($data);
    }

    /**
     * Check if email exists for user
     */
    public function emailExistsForUser(string $email, int $userId): bool
    {
        return Client::where('email', $email)
            ->where('user_id', $userId)
            ->exists();
    }
}
