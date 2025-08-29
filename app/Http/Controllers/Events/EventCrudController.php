<?php

namespace App\Http\Controllers\Events;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\Events\EventUpdateService;
use App\Services\Events\EventEditService;
use App\Services\Events\EventCreateService;
use App\Services\Events\EventStoreService;
use App\Services\Events\EventDeleteService;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class EventCrudController extends Controller
{
    public function __construct(
        private readonly EventUpdateService $eventUpdateService,
        private readonly EventEditService $eventEditService,
        private readonly EventCreateService $eventCreateService,
        private readonly EventStoreService $eventStoreService,
        private readonly EventDeleteService $eventDeleteService,
    ) {}

    /**
     * Show the form for creating a new event with skeleton pattern
     */
    public function create(Request $request): Response
    {
        $projectId = $request->query('project_id') ? (int) $request->query('project_id') : null;

        // Pattern skeleton : page avec données skeleton + données pour fetch AJAX
        return Inertia::render('Events/Create/Index', [
            'projectId' => $projectId,
            'skeletonData' => $this->eventCreateService->getSkeletonData(),
            'data' => Inertia::optional(function () use ($projectId) {
                try {
                    return $this->eventCreateService->getCompleteData($projectId);
                } catch (\Exception $e) {
                    return [
                        'error' => true,
                        'message' => 'Une erreur est survenue lors du chargement des données.',
                        'debug_message' => app()->environment('local') ? $e->getMessage() : null
                    ];
                }
            }),
        ]);
    }

    /**
     * Store a new event
     */
    public function store(StoreEventRequest $request): RedirectResponse
    {

        $project = \App\Models\Project::find($request->validated()['project_id']);
        if (!$project || $project->client->user_id !== auth()->id()) {
            return back()->withErrors(['general' => "Accès non autorisé."]);
        }

        try {
            // Utiliser le service pour créer l'événement
            $event = $this->eventStoreService->storeEvent($request->validated());

            return redirect()->route('events.show', $event)
                ->with('success', 'Événement créé avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['general' => 'Une erreur est survenue lors de la création.'])
                ->withInput();
        }
    }



    /**
     * Show the form for editing an event with skeleton pattern
     */
    public function edit(Event $event): Response
    {
        // Pattern skeleton : page avec données skeleton + ID pour fetch AJAX
        return Inertia::render('Events/Edit/Index', [
            'eventId' => $event->id,
            'skeletonData' => $this->eventEditService->getSkeletonData(),
            'data' => Inertia::optional(function () use ($event) {
                try {
                    return $this->eventEditService->getCompleteData($event->id);
                } catch (\Exception $e) {
                    return [
                        'error' => true,
                        'message' => 'Une erreur est survenue lors du chargement des données.',
                        'debug_message' => app()->environment('local') ? $e->getMessage() : null
                    ];
                }
            }),
        ]);
    }

    /**
     * Update an event
     */
    public function update(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        if($event->project->client->user_id !== auth()->id()){
            return back()->withErrors(['general' => "Accès non autorisé."]);
        }

        try {
            // Utiliser le service pour mettre à jour l'événement
            $updatedEvent = $this->eventUpdateService->updateEvent($event, $request->validated());

            return redirect()->route('events.show', $updatedEvent)
                ->with('success', 'Événement mis à jour avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['general' => 'Une erreur est survenue lors de la mise à jour.'])
                ->withInput();
        }
    }



    /**
     * Update event status
     */
    public function updateStatus(Request $request, Event $event): RedirectResponse
    {

        if($event->project->client->user_id !== auth()->id()){
            return back()->withErrors(['access' => "Accès non autorisé."]);
        }

        $validated = $request->validate([
            'status' => 'required|string',
        ]);

        try {
            // Mise à jour du statut avec date de complétion si applicable
            $updateData = ['status' => $validated['status']];

            // Si on marque comme fait/envoyé, ajouter la date de complétion
            if (in_array($validated['status'], ['done', 'sent'])) {
                $updateData['completed_at'] = now();
            }

            $updatedEvent = $this->eventUpdateService->updateEvent($event, $updateData);

            return redirect()->route('events.show', $updatedEvent)
                ->with('success', 'Statut mis à jour avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['general' => 'Une erreur est survenue lors de la mise à jour.'])
                ->withInput();
        }
    }

    /**
     * Update event payment status
     */
    public function updatePaymentStatus(Request $request, Event $event): RedirectResponse
    {

        if($event->project->client->user_id !== auth()->id()){
            return back()->withErrors(['general' => "Accès non autorisé."]);
        }

        $validated = $request->validate([
            'payment_status' => 'required|string|in:pending,paid',
        ]);

        try {
            // Vérifier que c'est un événement de facturation
            if ($event->event_type !== 'billing') {
                return back()->with('error', 'Seuls les événements de facturation peuvent avoir leur statut de paiement modifié.');
            }

            $updateData = ['payment_status' => $validated['payment_status']];

            // Logique déplacée vers EventUpdateService::handlePaymentStatusChange
            // Ne pas gérer paid_at ici pour éviter la duplication

            $updatedEvent = $this->eventUpdateService->updateEvent($event, $updateData);

            $message = $validated['payment_status'] === 'paid'
                ? 'Événement marqué comme payé.'
                : 'Événement marqué comme en attente de paiement.';

            return redirect()->route('events.show', $updatedEvent)
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()
                ->withErrors(['general' => 'Une erreur est survenue lors de la mise à jour.'])
                ->withInput();
        }
    }



    /**
     * Remove an event
     */
    public function destroy(Event $event): RedirectResponse
    {
        if($event->project->client->user_id !== auth()->id()){
            return back()->withErrors(['general' => "Accès non autorisé."]);
        }
        try {
            // Utiliser le service de suppression dédié
            $result = $this->eventDeleteService->deleteEvent($event);

            return redirect()->route('events.index')
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()
                ->withErrors(['general' => 'Une erreur est survenue lors de la suppression'])
                ->withInput();
        }
    }
}
