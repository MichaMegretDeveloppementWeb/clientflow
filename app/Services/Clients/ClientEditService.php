<?php

namespace App\Services\Clients;

use App\Repositories\Contracts\Clients\ClientEditRepositoryInterface;
use App\DTOs\ClientDTO;
use App\Models\Client;

class ClientEditService
{
    public function __construct(
        private readonly ClientEditRepositoryInterface $clientRepository,
    ) {}

    /**
     * Get skeleton data structure for loading states
     */
    public function getSkeletonData(): array
    {
        return [
            'client' => [
                'id' => null,
                'name' => '',
                'company' => '',
                'email' => '',
                'phone' => '',
                'address' => '',
                'notes' => '',
                'created_at' => '',
                'updated_at' => '',
            ]
        ];
    }

    /**
     * Get complete client data for editing
     */
    public function getCompleteData(int $clientId): array
    {
        try {

            $client = $this->clientRepository->findClientForEdit($clientId);

            if (!$client) {
                return [
                    'client' => [],
                    'errors' => [
                        'client' => 'Client introuvable ou accès non autorisé',
                    ]
                ];
            }

            return [
                'client' => ClientDTO::fromModel($client)->toEditArray(),
                'errors' => []
            ];

        }catch (\Exception $e) {
            throw new \Exception("Erreurs lors du chargement des données.");
        }

    }

    /**
     * Get client details for editing - Alias for getCompleteData
     */
    public function getClientForEdit(int $clientId): array
    {
        return $this->getCompleteData($clientId);
    }
}
