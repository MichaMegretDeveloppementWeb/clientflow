<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use App\Models\Event;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Configuration commune pour tous les tests - sans withoutExceptionHandling pour debug
        // $this->withoutExceptionHandling();
        // $this->seed(); // Ne pas faire de seed automatique pour les tests unitaires
    }

    /**
     * Créer un utilisateur authentifié pour les tests
     */
    protected function actingAsUser(array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $this->actingAs($user);
        return $user;
    }

    /**
     * Créer un client avec des données personnalisées
     */
    protected function createClient(array $attributes = []): Client
    {
        return Client::factory()->create($attributes);
    }

    /**
     * Créer un projet avec des données personnalisées
     */
    protected function createProject(array $attributes = []): Project
    {
        return Project::factory()->create($attributes);
    }

    /**
     * Créer un événement avec des données personnalisées
     */
    protected function createEvent(array $attributes = []): Event
    {
        return Event::factory()->create($attributes);
    }

    /**
     * Créer un ensemble complet : Client -> Project -> Events
     */
    protected function createCompleteSetup(): array
    {
        $client = $this->createClient();
        $project = $this->createProject(['client_id' => $client->id]);
        
        $stepEvent = $this->createEvent([
            'project_id' => $project->id,
            'event_type' => 'step'
        ]);
        
        $billingEvent = $this->createEvent([
            'project_id' => $project->id,
            'event_type' => 'billing'
        ]);

        return [
            'client' => $client,
            'project' => $project,
            'stepEvent' => $stepEvent,
            'billingEvent' => $billingEvent
        ];
    }

    /**
     * Assertions personnalisées pour les réponses Inertia
     */
    protected function assertInertiaResponse($response, string $component, array $props = [])
    {
        $response->assertStatus(200);
        
        if (!empty($props)) {
            foreach ($props as $key => $value) {
                $response->assertInertia(fn ($page) => $page->has($key));
            }
        }
    }

    /**
     * Helper pour tester les validations de formulaire
     */
    protected function assertValidationErrors($response, array $fields)
    {
        $response->assertSessionHasErrors($fields);
        foreach ($fields as $field) {
            $this->assertNotNull(session("errors.{$field}"));
        }
    }
}
