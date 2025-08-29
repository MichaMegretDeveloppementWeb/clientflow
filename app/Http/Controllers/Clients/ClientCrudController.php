<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Services\Clients\ClientCreateService;
use App\Services\Clients\ClientStoreService;
use App\Services\Clients\ClientEditService;
use App\Services\Clients\ClientUpdateService;
use App\Services\Clients\ClientDeleteService;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\DTOs\ClientDTO;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ClientCrudController extends Controller
{
    public function __construct(
        private readonly ClientCreateService $clientCreateService,
        private readonly ClientStoreService $clientStoreService,
        private readonly ClientEditService $clientEditService,
        private readonly ClientUpdateService $clientUpdateService,
        private readonly ClientDeleteService $clientDeleteService
    ) {}

    /**
     * Show the form for creating a new client with skeleton pattern
     */
    public function create(): Response
    {
        // Pattern skeleton : page avec données skeleton + données pour fetch AJAX
        return Inertia::render('Clients/Create/Index', [
            'skeletonData' => $this->clientCreateService->getSkeletonData(),
            'data' => Inertia::optional(function () {
                try {
                    return $this->clientCreateService->getCompleteData();
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
     * Store a newly created client
     */
    public function store(StoreClientRequest $request): RedirectResponse
    {

        try {
            // Utiliser le service pour créer le client
            $client = $this->clientStoreService->storeClient($request->validated());

            return redirect()->route('clients.show', $client)
                ->with('success', 'Client créé avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['general' => 'Une erreur est survenue lors de la création.'])
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified client
     */
    public function edit(int $clientId): Response
    {
        // Pattern skeleton : page avec données skeleton + ID pour fetch AJAX
        return Inertia::render('Clients/Edit/Index', [
            'clientId' => $clientId,
            'skeletonData' => $this->clientEditService->getSkeletonData(),
            'data' => Inertia::optional(function () use ($clientId) {
                try {
                    return $this->clientEditService->getCompleteData($clientId);
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
     * Update the specified client
     */
    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        if($client->user_id !== auth()->id()){
            return back()->withErrors(['general' => "Accès non autorisé."]);
        }

        try {
            // Utiliser le service pour mettre à jour le client
            $updatedClient = $this->clientUpdateService->updateClient($client, $request->validated());

            return redirect()->route('clients.show', $updatedClient)
                ->with('success', 'Client mis à jour avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['general' => 'Une erreur est survenue lors de la mise à jour.'])
                ->withInput();
        }
    }

    /**
     * Remove the specified client
     */
    public function destroy(Client $client): RedirectResponse
    {
        if($client->user_id !== auth()->id()){
            return back()->withErrors(['general' => "Accès non autorisé."]);
        }
        try {
            // Utiliser le service pour supprimer le client (avec vérifications)
            $this->clientDeleteService->deleteClient($client);

            return redirect()->route('clients.index')
                ->with('success', 'Client supprimé avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['general' => 'Une erreur est survenue lors de la supression.'])
                ->withInput();
        }
    }
}
