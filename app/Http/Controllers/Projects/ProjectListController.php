<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Services\Projects\ProjectListService;
use App\DTOs\ProjectDTO;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectListController extends Controller
{
    public function __construct(
        private readonly ProjectListService $projectListService
    ) {}

    /**
     * Display projects list page with skeleton data for immediate rendering
     */
    public function index(Request $request): Response
    {
        // Récupérer les filtres depuis l'URL pour les préserver lors du rafraîchissement
        $filters = $request->only(['search', 'sort_by', 'sort_order', 'status', 'client_id', 'has_overdue_tasks', 'has_payment_overdue']);

        $perPage = (int) $request->get('per_page', 15);

        $skeletonData = $this->projectListService->getSkeletonData($perPage);

        return Inertia::render('Projects/List/Index', [
            // Données skeleton immédiatement disponibles
            'skeletonData' => array_merge($skeletonData, [
                'filters' => $filters,
            ]),
            
            // Données optionnelles - chargées via fetch côté client au premier rendu
            'projectsData' => Inertia::optional(function () use ($filters, $perPage) {
                try {
                    return $this->projectListService->getCompleteData($filters, $perPage);
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
