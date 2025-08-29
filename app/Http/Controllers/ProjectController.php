<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Services\Project\ProjectService;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\DTOs\ProjectDTO;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectService $projectService
    ) {}



    public function index(Request $request): Response
    {
        // Récupérer les filtres depuis l'URL pour les préserver lors du rafraîchissement
        $filters = $request->only(['search', 'sort_by', 'sort_order', 'status', 'client_id', 'has_overdue_tasks', 'has_payment_overdue']);

        $skeletonData = [
            'projects' => [
                'data' => [],
                'links' => [],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 10,
                    'total' => 0,
                ]
            ],
            'clients' => [],
            'stats' => [
                'active' => 0,
                'completed' => 0,
                'on_hold' => 0,
                'total' => 0,
            ],
            'filters' => $filters, // Transmettre les filtres au skeleton
            'skeleton_mode' => true // Force TOUJOURS le mode skeleton au chargement initial
        ];

        return Inertia::render('Projects/Index', $skeletonData);
    }


    /**
     * Route GET AJAX dédiée pour charger les vraies données (sans modification d'URL)
     */
    public function getData(Request $request)
    {
        $filters = $request->only(['search', 'sort_by', 'sort_order', 'status', 'client_id', 'has_overdue_tasks', 'has_payment_overdue']);
        
        // Debug temporaire - à supprimer après test
        \Log::info('Filters received:', $filters);

        $perPage = (int) $request->get('per_page', 10);
        $projects = $this->projectService->getPaginatedProjects($filters, $perPage);

        // Convertir en DTOs
        $projectDTOs = ProjectDTO::collection($projects->getCollection());

        // Récupérer les statistiques et clients via le service
        $stats = $this->projectService->getGlobalStatistics();
        $clients = $this->projectService->getAvailableClients();

        // Retourner JSON pour AJAX (pas Inertia)
        return response()->json([
            'projects' => [
                'data' => $projectDTOs->map(fn(ProjectDTO $dto) => $dto->toArray()),
                'links' => $projects->linkCollection(),
                'meta' => [
                    'current_page' => $projects->currentPage(),
                    'last_page' => $projects->lastPage(),
                    'per_page' => $projects->perPage(),
                    'total' => $projects->total(),
                ]
            ],
            'stats' => $stats,
            'clients' => $clients,
            'filters' => $filters
        ]);
    }




    public function show(Project $project): Response
    {
        // Utiliser le service pour obtenir les détails complets du projet
        $projectDetails = $this->projectService->getProjectDetails($project->id);

        // Convertir en DTO
        $projectDTO = ProjectDTO::fromArray($projectDetails['project']);

        return Inertia::render('Projects/Show', [
            'project' => $projectDTO->toArray(),
            'statistics' => $projectDetails['statistics'],
            'timeline' => $projectDetails['timeline'],
            'financial_summary' => $projectDetails['financial_summary'],
        ]);
    }

    public function create(Request $request): Response
    {
        $clients = $this->projectService->getAvailableClients();
        $selectedClientId = $request->get('client_id');

        return Inertia::render('Projects/Create', [
            'clients' => $clients,
            'selectedClientId' => $selectedClientId
        ]);
    }

    public function store(StoreProjectRequest $request)
    {
        // Utiliser le service pour créer le projet
        $project = $this->projectService->createProject($request->validated());

        return redirect()->route('projects.show', $project)
            ->with('success', 'Projet créé avec succès.');
    }

    public function edit(Project $project): Response
    {
        $clients = $this->projectService->getAvailableClients();

        $projectDTO = ProjectDTO::fromModel($project);

        return Inertia::render('Projects/Edit', [
            'project' => $projectDTO->toArray(),
            'clients' => $clients
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        // Utiliser le service pour mettre à jour le projet
        $updatedProject = $this->projectService->updateProject($project, $request->validated());

        return redirect()->route('projects.show', $updatedProject)
            ->with('success', 'Projet mis à jour avec succès.');
    }



    public function destroy(Project $project)
    {
        try {
            // Utiliser le service pour supprimer le projet (avec vérifications)
            $this->projectService->deleteProject($project);

            return redirect()->route('projects.index')
                ->with('success', 'Projet supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
