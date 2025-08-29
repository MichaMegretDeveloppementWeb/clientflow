<?php

namespace App\Repositories\Events;

use App\Models\Event;
use App\Repositories\Contracts\Events\EventDeleteRepositoryInterface;

class EventDeleteRepository implements EventDeleteRepositoryInterface
{
    /**
     * Supprimer un événement
     */
    public function delete(Event $event): bool
    {
        return $event->delete();
    }

    /**
     * Récupérer le projet associé à l'événement avant suppression
     */
    public function getEventProject(Event $event): ?object
    {
        return $event->project;
    }

    /**
     * Vérifier si l'événement peut être supprimé
     */
    public function canBeDeleted(Event $event): bool
    {
        // Logique métier pour vérifier si un événement peut être supprimé
        // Par exemple, vérifier s'il n'y a pas de dépendances critiques

        // Pour l'instant, tous les événements peuvent être supprimés
        return true;
    }
}
