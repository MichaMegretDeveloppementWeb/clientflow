<?php

namespace App\Http\Controllers\Events;

use App\Http\Controllers\Controller;
use App\Services\Events\EventListService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class EventListController extends Controller
{
    public function __construct(
        private readonly EventListService $eventListService
    ) {}

    /**
     * Display events list page with skeleton data for immediate rendering
     */
    public function index(Request $request): Response
    {
        // Récupérer les filtres depuis l'URL pour les préserver lors du rafraîchissement
        $filters = $request->only(['search', 'event_type', 'status', 'project_id', 'client_id', 'payment_status', 'overdue', 'payment_overdue', 'sort', 'direction']);

        $perPage = (int) $request->get('per_page', 10);

        $skeletonData = $this->eventListService->getSkeletonData();

        return Inertia::render('Events/List/Index', [
            // Données skeleton immédiatement disponibles
            'skeletonData' => array_merge($skeletonData, [
                'filters' => $filters,
            ]),
            
            // Données optionnelles - chargées via fetch côté client au premier rendu
            'eventsData' => Inertia::optional(function () use ($filters, $perPage) {
                try {
                    return $this->eventListService->getCompleteData($filters, $perPage);
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
