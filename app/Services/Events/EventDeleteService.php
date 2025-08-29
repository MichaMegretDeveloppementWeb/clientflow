<?php

namespace App\Services\Events;

use App\Models\Event;
use App\Repositories\Contracts\Events\EventDeleteRepositoryInterface;
use Exception;

class EventDeleteService
{
    public function __construct(
        private readonly EventDeleteRepositoryInterface $eventDeleteRepository
    ) {}

    /**
     * Supprimer un événement
     */
    public function deleteEvent(Event $event): array
    {
        // Vérifier si l'événement peut être supprimé
        if (!$this->eventDeleteRepository->canBeDeleted($event)) {
            throw new Exception("Cet événement ne peut pas être supprimé.");
        }

        // Récupérer le projet avant suppression pour la redirection
        $project = $this->eventDeleteRepository->getEventProject($event);

        // Supprimer l'événement
        $deleted = $this->eventDeleteRepository->delete($event);

        if (!$deleted) {
            throw new Exception("Erreur lors de la suppression de l'événement.");
        }

        return [
            'success' => true,
            'project' => $project,
            'message' => 'Événement supprimé avec succès.'
        ];
    }
}
