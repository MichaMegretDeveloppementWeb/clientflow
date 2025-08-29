<?php

namespace App\Repositories\Contracts\Events;

use App\Models\Event;

interface EventDeleteRepositoryInterface
{
    /**
     * Supprimer un événement
     */
    public function delete(Event $event): bool;

    /**
     * Récupérer le projet associé à l'événement avant suppression
     */
    public function getEventProject(Event $event): ?object;

    /**
     * Vérifier si l'événement peut être supprimé
     */
    public function canBeDeleted(Event $event): bool;
}
