<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\Projects\ProjectCreateService;
use App\Services\Projects\ProjectStoreService;
use App\Services\Projects\ProjectEditService;
use App\Services\Projects\ProjectUpdateService;
use App\Services\Projects\ProjectDeleteService;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\DTOs\ProjectDTO;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProjectCrudController extends Controller
{
    public function __construct(
        private readonly ProjectCreateService $projectCreateService,
        private readonly ProjectStoreService $projectStoreService,
        private readonly ProjectEditService $projectEditService,
        private readonly ProjectUpdateService $projectUpdateService,
        private readonly ProjectDeleteService $projectDeleteService
    ) {}

    /**
     * Show the form for creating a new project with skeleton pattern
     */
    public function create(Request $request): Response
    {
        $selectedClientId = $request->get('client_id') ? (int) $request->get('client_id') : null;

        // Pattern skeleton : page avec données skeleton + données pour fetch AJAX
        return Inertia::render('Projects/Create/Index', [
            'skeletonData' => $this->projectCreateService->getSkeletonData(),
            'data' => Inertia::optional(function () use ($selectedClientId) {
                try {
                    return $this->projectCreateService->getCompleteData($selectedClientId);
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
     * Store a newly created project
     */
    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $client = \App\Models\Client::find($request->validated()['client_id']);
        if (!$client || $client->user_id !== auth()->id()) {
            return back()->withErrors(['general' => "Accès non autorisé."]);
        }

        try {
            // Utiliser le service pour créer le projet
            $project = $this->projectStoreService->storeProject($request->validated());

            return redirect()->route('projects.show', $project)
                ->with('success', 'Projet créé avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['general' => 'Une erreur est survenue lors de la création.'])
                ->withInput();
        }
    }

    /**
     * Show the form for editing a project with skeleton pattern
     */
    public function edit(Project $project): Response
    {
        // Pattern skeleton : page avec données skeleton + ID pour fetch AJAX
        return Inertia::render('Projects/Edit/Index', [
            'projectId' => $project->id,
            'skeletonData' => $this->projectEditService->getSkeletonData(),
            'data' => Inertia::optional(function () use ($project) {
                try {
                    return $this->projectEditService->getCompleteData($project->id);
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
     * Update a project
     */
    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        if($project->client->user_id !== auth()->id()){
            return back()->withErrors(['general' => "Accès non autorisé."]);
        }

        try {
            $updatedProject = $this->projectUpdateService->updateProject($project, $request->validated());

            return redirect()->route('projects.show', $updatedProject)
                ->with('success', 'Projet mis à jour avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['general' => 'Une erreur est survenue lors de la mise à jour.'])
                ->withInput();
        }
    }

    public function destroy(Project $project): RedirectResponse
    {
        if($project->client->user_id !== auth()->id()){
            return back()->withErrors(['general' => "Accès non autorisé."]);
        }
        try {
            // Utiliser le service pour supprimer le projet (avec vérifications)
            $this->projectDeleteService->deleteProject($project);

            return redirect()->route('projects.index')
                ->with('success', 'Projet supprimé avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['general' => 'Une erreur est survenue lors de la supression.']);
        }
    }
}
