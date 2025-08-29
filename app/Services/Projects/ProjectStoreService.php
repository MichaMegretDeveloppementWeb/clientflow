<?php

namespace App\Services\Projects;

use App\Models\Project;
use App\Repositories\Contracts\Projects\ProjectStoreRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ProjectStoreService
{
    public function __construct(
        private readonly ProjectStoreRepositoryInterface $projectStoreRepository
    ) {}

    /**
     * Store a new project
     */
    public function storeProject(array $validatedData): Project
    {
        return DB::transaction(function () use ($validatedData) {
            // Preprocessing des données
            $processedData = $this->preprocessProjectData($validatedData);
            
            // Validation métier
            $this->validateBusinessRules($processedData);
            
            // Création du projet
            $project = $this->projectStoreRepository->create($processedData);
            
            // Post-processing
            $this->postProcessProject($project, $processedData);
            
            return $project;
        });
    }

    /**
     * Preprocess project data before storage
     */
    private function preprocessProjectData(array $data): array
    {
        // Ajouter l'ID utilisateur
        $data['user_id'] = auth()->id();
        
        // Conversion budget si présent
        if (isset($data['budget']) && $data['budget'] !== '') {
            $data['budget'] = (float) $data['budget'];
        } else {
            $data['budget'] = null;
        }
        
        // Nettoyage des dates vides
        foreach (['start_date', 'end_date'] as $field) {
            if (isset($data[$field]) && $data[$field] === '') {
                $data[$field] = null;
            }
        }
        
        // Nettoyage des champs texte
        foreach (['name', 'description'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        return $data;
    }

    /**
     * Validate business rules
     */
    private function validateBusinessRules(array $data): void
    {
        // Vérifier cohérence des dates
        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            if ($data['end_date'] < $data['start_date']) {
                throw new \Exception('La date de fin ne peut pas être antérieure à la date de début.');
            }
        }
        
        // Vérifier unicité nom projet pour ce client
        if ($this->projectStoreRepository->projectNameExistsForUserAndClient(
            $data['name'], $data['user_id'], $data['client_id']
        )) {
            throw new \Exception('Un projet avec ce nom existe déjà pour ce client.');
        }
    }

    /**
     * Post-process the created project
     */
    private function postProcessProject(Project $project, array $data): void
    {
        // Log de création
        \Log::info("Project created", [
            'project_id' => $project->id,
            'project_name' => $project->name,
            'client_id' => $project->client_id,
            'user_id' => $project->user_id,
            'budget' => $project->budget,
        ]);
    }
}
