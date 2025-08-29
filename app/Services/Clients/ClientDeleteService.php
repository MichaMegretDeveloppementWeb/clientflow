<?php

namespace App\Services\Clients;

use App\Models\Client;
use App\Repositories\Contracts\Clients\ClientDeleteRepositoryInterface;
use Exception;

class ClientDeleteService
{
    public function __construct(
        private readonly ClientDeleteRepositoryInterface $clientRepository
    ) {}

    /**
     * Delete a client with verification
     */
    public function deleteClient(Client $client): bool
    {

        try {
            $deleted = $this->clientRepository->delete($client);
        }catch (\Exception $e) {
            throw new Exception('Erreur innatendue lors de la suppression. Vueillez réessayer.');
        }


        if ($deleted) {
            $this->logClientActivity($client, 'deleted');
        }

        return $deleted;
    }

    /**
     * Log client activity
     */
    private function logClientActivity(Client $client, string $action): void
    {
        \Log::info("Client {$action}", ['client_id' => $client->id, 'action' => $action]);
    }
}
