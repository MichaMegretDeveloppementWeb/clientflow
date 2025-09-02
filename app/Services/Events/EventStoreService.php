<?php

namespace App\Services\Events;

use App\Models\Event;
use App\Repositories\Contracts\Events\EventStoreRepositoryInterface;
use App\DTOs\EventDTO;

class EventStoreService
{
    public function __construct(
        private readonly EventStoreRepositoryInterface $eventStoreRepository,
    ) {}

    /**
     * Store a new event
     */
    public function storeEvent(array $validatedData): Event
    {
        // Preprocessing des données
        $processedData = $this->preprocessEventData($validatedData);
        
        // Validation métier
        $this->validateBusinessRules($processedData);
        
        // Création de l'événement
        $event = $this->eventStoreRepository->create($processedData);
        
        // Post-processing si nécessaire
        $this->postProcessEvent($event, $processedData);
        
        return $event;
    }

    /**
     * Preprocess event data before storage
     */
    private function preprocessEventData(array $data): array
    {
        // Définir les valeurs par défaut
        $data['created_date'] = $data['created_date'] ?? now()->format('Y-m-d');
        
        // Définir le statut par défaut selon le type d'événement
        if (!isset($data['status'])) {
            $data['status'] = $data['event_type'] === 'billing' ? 'to_send' : 'todo';
        }
        
        // Définir le payment_status par défaut pour les événements de facturation
        if ($data['event_type'] === 'billing' && !isset($data['payment_status'])) {
            $data['payment_status'] = 'pending';
        }

        // Nettoyer les champs selon le type d'événement
        if ($data['event_type'] === 'step') {
            // Pour les étapes, retirer les champs de facturation
            unset($data['amount'], $data['payment_status'], $data['send_date'], $data['payment_due_date'], $data['paid_at']);
        } else {
            // Pour la facturation, retirer les champs d'étape
            unset($data['execution_date']);
        }

        return $data;
    }

    /**
     * Validate business rules
     */
    private function validateBusinessRules(array $data): void
    {
        // Vérifier que le projet existe et appartient à l'utilisateur
        if (!$this->eventStoreRepository->projectBelongsToUser($data['project_id'])) {
            throw new \Exception('Le projet sélectionné n\'existe pas ou vous n\'y avez pas accès.');
        }

        // Vérifier les dates selon le type d'événement
        $this->validateEventDates($data);
    }

    /**
     * Validate event dates
     */
    private function validateEventDates(array $data): void
    {
        if (!isset($data['created_date'])) {
            return;
        }

        $createdAt = \Carbon\Carbon::parse($data['created_date']);

        // Vérifier la date d'exécution pour les étapes
        if ($data['event_type'] === 'step' && isset($data['execution_date'])) {
            $executionDate = \Carbon\Carbon::parse($data['execution_date']);
            if ($executionDate->lt($createdAt)) {
                throw new \Exception('La date d\'exécution ne peut pas être antérieure à la date de création.');
            }
        }

        // Vérifier les dates pour la facturation
        if ($data['event_type'] === 'billing') {
            if (isset($data['send_date'])) {
                $sendDate = \Carbon\Carbon::parse($data['send_date']);
                if ($sendDate->lt($createdAt)) {
                    throw new \Exception('La date d\'envoi ne peut pas être antérieure à la date de création.');
                }
            }

            if (isset($data['payment_due_date']) && isset($data['send_date'])) {
                $paymentDueDate = \Carbon\Carbon::parse($data['payment_due_date']);
                $sendDate = \Carbon\Carbon::parse($data['send_date']);
                if ($paymentDueDate->lt($sendDate)) {
                    throw new \Exception('La date d\'échéance ne peut pas être antérieure à la date d\'envoi.');
                }
            }
        }
    }

    /**
     * Post-process the created event
     */
    private function postProcessEvent(Event $event, array $data): void
    {
        // Actions post-création si nécessaire
        // Par exemple: notifications, logs, etc.
    }
}