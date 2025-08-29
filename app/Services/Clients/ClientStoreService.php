<?php

namespace App\Services\Clients;

use App\Models\Client;
use App\Repositories\Contracts\Clients\ClientStoreRepositoryInterface;

class ClientStoreService
{
    public function __construct(
        private readonly ClientStoreRepositoryInterface $clientStoreRepository,
    ) {}

    /**
     * Store a new client
     */
    public function storeClient(array $validatedData): Client
    {
        // Preprocessing des données
        $processedData = $this->preprocessClientData($validatedData);
        
        // Validation métier
        $this->validateBusinessRules($processedData);
        
        // Création du client
        $client = $this->clientStoreRepository->create($processedData);
        
        // Post-processing si nécessaire
        $this->postProcessClient($client, $processedData);
        
        return $client;
    }

    /**
     * Preprocess client data before storage
     */
    private function preprocessClientData(array $data): array
    {
        // Ajouter l'ID utilisateur
        $data['user_id'] = auth()->id();
        
        // Nettoyer le numéro de téléphone
        if (isset($data['phone']) && !empty($data['phone'])) {
            $data['phone'] = $this->formatPhoneNumber($data['phone']);
        }
        
        // Nettoyer l'email
        if (isset($data['email']) && !empty($data['email'])) {
            $data['email'] = strtolower(trim($data['email']));
        }
        
        // Nettoyer les champs texte
        foreach (['name', 'company', 'address', 'notes'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        return $data;
    }

    /**
     * Validate business rules
     */
    private function validateBusinessRules(array $data): void
    {
        // Vérifier l'unicité de l'email pour cet utilisateur
        if (isset($data['email']) && !empty($data['email'])) {
            if ($this->clientStoreRepository->emailExistsForUser($data['email'], $data['user_id'])) {
                throw new \Exception('Un client avec cette adresse email existe déjà.');
            }
        }
    }

    /**
     * Format phone number
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Nettoyer le numéro de téléphone
        return preg_replace('/[^0-9+]/', '', $phone);
    }

    /**
     * Post-process the created client
     */
    private function postProcessClient(Client $client, array $data): void
    {
        // Actions post-création si nécessaire
        // Par exemple: notifications, logs, etc.
        \Log::info("Client created", ['client_id' => $client->id]);
    }
}