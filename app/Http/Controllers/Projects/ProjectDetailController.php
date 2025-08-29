<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\Projects\ProjectDetailService;
use Inertia\Inertia;
use Inertia\Response;

class ProjectDetailController extends Controller
{
    public function __construct(
        private readonly ProjectDetailService $projectDetailService
    ) {}

    /**
     * Display project detail page with skeleton pattern
     */
    public function show(Project $project): Response
    {
        // Pattern skeleton : page avec données skeleton + ID pour fetch AJAX
        return Inertia::render('Projects/Detail/Index', [
            'projectId' => $project->id,
            'skeletonData' => $this->projectDetailService->getSkeletonData(),
            'data' => Inertia::optional(function () use ($project) {
                try {
                    return $this->projectDetailService->getCompleteData($project->id);
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
