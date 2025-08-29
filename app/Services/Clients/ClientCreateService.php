<?php

namespace App\Services\Clients;

use App\Repositories\Contracts\Clients\ClientCreateRepositoryInterface;

class ClientCreateService
{
    public function __construct(
        private readonly ClientCreateRepositoryInterface $clientCreateRepository,
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
            ],
            'errors' => []
        ];
    }

    /**
     * Get complete data for client creation
     */
    public function getCompleteData(): array
    {

        try {

            return [
                'client' => [
                    'id' => null,
                    'name' => '',
                    'company' => '',
                    'email' => '',
                    'phone' => '',
                    'address' => '',
                    'notes' => '',
                    'created_at' => now()->format('Y-m-d'),
                ],
                'errors' => []
            ];

        }catch (\Exception $e) {
            throw new \Exception("Erreurs lors du chargement des données.");
        }

    }
}
