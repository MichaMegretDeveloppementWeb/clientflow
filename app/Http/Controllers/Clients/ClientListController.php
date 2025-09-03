<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Services\Clients\ClientListService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class ClientListController extends Controller
{
    public function __construct(
        private readonly ClientListService $clientListService
    ) {}

    /**
     * Display clients list page with skeleton data for immediate rendering
     */
    public function index(Request $request): Response
    {
        // Récupérer les filtres depuis l'URL pour les préserver lors du rafraîchissement
        $filters = $request->only(['search', 'sort_by', 'sort_order', 'has_projects', 'has_active_projects', 'has_overdue_payments']);

        $perPage = (int) $request->get('per_page', 10);

        $skeletonData = $this->clientListService->getSkeletonData($perPage);

        return Inertia::render('Clients/List/Index', [
            // Données skeleton immédiatement disponibles
            'skeletonData' => array_merge($skeletonData, [
                'filters' => $filters,
            ]),

            // Données optionnelles - chargées via fetch côté client au premier rendu
            'clientsData' => Inertia::optional(function () use ($filters, $perPage) {
                try {
                    return $this->clientListService->getCompleteData($filters, $perPage);
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

}
