<?php

namespace App\Services\Clients;

use App\Models\Client;
use App\Repositories\Contracts\Clients\ClientUpdateRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ClientUpdateService
{
    public function __construct(
        private readonly ClientUpdateRepositoryInterface $clientRepository
    ) {}

    /**
     * Update existing client
     */
    public function updateClient(Client $client, array $data): Client
    {
        return DB::transaction(function () use ($client, $data) {
            // Clean phone number
            if (isset($data['phone'])) {
                $data['phone'] = $this->formatPhoneNumber($data['phone']);
            }
            
            $updatedClient = $this->clientRepository->update($client, $data);
            
            // Log activity
            $this->logClientActivity($updatedClient, 'updated');
            
            return $updatedClient;
        });
    }

    /**
     * Format phone number
     */
    private function formatPhoneNumber(string $phone): string
    {
        return preg_replace('/[^0-9+]/', '', $phone);
    }

    /**
     * Log client activity
     */
    private function logClientActivity(Client $client, string $action): void
    {
        \Log::info("Client {$action}", ['client_id' => $client->id, 'action' => $action]);
    }
}