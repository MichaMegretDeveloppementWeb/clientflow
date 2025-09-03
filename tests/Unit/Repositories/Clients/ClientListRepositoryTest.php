<?php

namespace Tests\Unit\Repositories\Clients;

use Tests\TestCase;
use App\Repositories\Clients\ClientListRepository;
use App\Models\Client;
use App\Models\Project;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;

class ClientListRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ClientListRepository $repository;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ClientListRepository();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_can_paginate_clients()
    {
        // Créer des clients pour l'utilisateur connecté
        $client1 = $this->createClient(['user_id' => $this->user->id, 'name' => 'Client A']);
        $client2 = $this->createClient(['user_id' => $this->user->id, 'name' => 'Client B']);
        
        // Créer un client pour un autre utilisateur (ne doit pas apparaître)
        $otherUser = User::factory()->create();
        $this->createClient(['user_id' => $otherUser->id, 'name' => 'Other Client']);

        $result = $this->repository->paginate(10, []);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(2, $result->total());
        $this->assertCount(2, $result->items());
        
        $clientNames = collect($result->items())->pluck('name')->toArray();
        $this->assertContains('Client A', $clientNames);
        $this->assertContains('Client B', $clientNames);
        $this->assertNotContains('Other Client', $clientNames);
    }

    public function test_can_search_clients()
    {
        $client1 = $this->createClient([
            'user_id' => $this->user->id,
            'name' => 'John Doe',
            'company' => 'Acme Corp'
        ]);
        $client2 = $this->createClient([
            'user_id' => $this->user->id,
            'name' => 'Jane Smith',
            'email' => 'jane@example.com'
        ]);
        $client3 = $this->createClient([
            'user_id' => $this->user->id,
            'name' => 'Bob Wilson',
            'company' => 'Tech Solutions'
        ]);

        // Test search by name
        $result = $this->repository->paginate(10, ['search' => 'John']);
        $this->assertEquals(1, $result->total());
        $this->assertEquals('John Doe', $result->items()[0]->name);

        // Test search by email
        $result = $this->repository->paginate(10, ['search' => 'jane@example']);
        $this->assertEquals(1, $result->total());
        $this->assertEquals('Jane Smith', $result->items()[0]->name);

        // Test search by company
        $result = $this->repository->paginate(10, ['search' => 'Acme']);
        $this->assertEquals(1, $result->total());
        $this->assertEquals('John Doe', $result->items()[0]->name);
    }

    public function test_can_filter_clients_with_projects()
    {
        $clientWithProjects = $this->createClient(['user_id' => $this->user->id]);
        $clientWithoutProjects = $this->createClient(['user_id' => $this->user->id]);
        
        $this->createProject(['client_id' => $clientWithProjects->id]);

        // Filter clients with projects
        $result = $this->repository->paginate(10, ['has_projects' => 'true']);
        $this->assertEquals(1, $result->total());
        $this->assertEquals($clientWithProjects->id, $result->items()[0]->id);

        // Filter clients without projects
        $result = $this->repository->paginate(10, ['has_projects' => 'false']);
        $this->assertEquals(1, $result->total());
        $this->assertEquals($clientWithoutProjects->id, $result->items()[0]->id);
    }

    public function test_can_filter_clients_with_active_projects()
    {
        $clientWithActiveProject = $this->createClient(['user_id' => $this->user->id]);
        $clientWithInactiveProject = $this->createClient(['user_id' => $this->user->id]);
        
        $this->createProject([
            'client_id' => $clientWithActiveProject->id,
            'status' => 'active'
        ]);
        $this->createProject([
            'client_id' => $clientWithInactiveProject->id,
            'status' => 'completed'
        ]);

        $result = $this->repository->paginate(10, ['has_active_projects' => 'true']);
        $this->assertEquals(1, $result->total());
        $this->assertEquals($clientWithActiveProject->id, $result->items()[0]->id);
    }

    public function test_can_filter_clients_with_overdue_payments()
    {
        $clientWithOverdue = $this->createClient(['user_id' => $this->user->id]);
        $clientWithoutOverdue = $this->createClient(['user_id' => $this->user->id]);
        
        $projectWithOverdue = $this->createProject(['client_id' => $clientWithOverdue->id]);
        $projectWithoutOverdue = $this->createProject(['client_id' => $clientWithoutOverdue->id]);
        
        // Create overdue billing event
        $this->createEvent([
            'project_id' => $projectWithOverdue->id,
            'event_type' => 'billing',
            'payment_status' => 'pending',
            'payment_due_date' => now()->subDays(5)
        ]);
        
        // Create non-overdue billing event
        $this->createEvent([
            'project_id' => $projectWithoutOverdue->id,
            'event_type' => 'billing',
            'payment_status' => 'paid',
            'payment_due_date' => now()->subDays(1)
        ]);

        $result = $this->repository->paginate(10, ['has_overdue_payments' => 'true']);
        $this->assertEquals(1, $result->total());
        $this->assertEquals($clientWithOverdue->id, $result->items()[0]->id);
    }

    public function test_can_sort_clients()
    {
        $client1 = $this->createClient([
            'user_id' => $this->user->id,
            'name' => 'Alpha Client',
            'created_at' => now()->subDays(2)
        ]);
        $client2 = $this->createClient([
            'user_id' => $this->user->id,
            'name' => 'Beta Client',
            'created_at' => now()->subDays(1)
        ]);

        // Sort by name ascending
        $result = $this->repository->paginate(10, [
            'sort_by' => 'name',
            'sort_order' => 'asc'
        ]);
        $this->assertEquals('Alpha Client', $result->items()[0]->name);
        $this->assertEquals('Beta Client', $result->items()[1]->name);

        // Sort by name descending
        $result = $this->repository->paginate(10, [
            'sort_by' => 'name',
            'sort_order' => 'desc'
        ]);
        $this->assertEquals('Beta Client', $result->items()[0]->name);
        $this->assertEquals('Alpha Client', $result->items()[1]->name);

        // Sort by created_at (default is desc)
        $result = $this->repository->paginate(10, [
            'sort_by' => 'created_at',
            'sort_order' => 'desc'
        ]);
        $this->assertEquals('Beta Client', $result->items()[0]->name);
    }

    public function test_can_get_global_statistics()
    {
        $client1 = $this->createClient(['user_id' => $this->user->id]);
        $client2 = $this->createClient(['user_id' => $this->user->id]);
        
        $project1 = $this->createProject(['client_id' => $client1->id, 'status' => 'active']);
        $project2 = $this->createProject(['client_id' => $client2->id, 'status' => 'completed']);
        
        $this->createEvent([
            'project_id' => $project1->id,
            'event_type' => 'billing',
            'amount' => 1000,
            'payment_status' => 'paid'
        ]);
        $this->createEvent([
            'project_id' => $project2->id,
            'event_type' => 'billing',
            'amount' => 500,
            'payment_status' => 'pending'
        ]);

        $stats = $this->repository->getGlobalStatistics();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total', $stats);
        $this->assertArrayHasKey('with_projects', $stats);
        $this->assertArrayHasKey('without_projects', $stats);
        $this->assertArrayHasKey('with_active_projects', $stats);
        
        $this->assertEquals(2, $stats['total']);
        $this->assertEquals(2, $stats['with_projects']); // Les deux clients ont des projets
        $this->assertEquals(0, $stats['without_projects']);
        $this->assertEquals(1, $stats['with_active_projects']); // Un seul avec projet actif
    }

    public function test_includes_financial_data()
    {
        $client = $this->createClient(['user_id' => $this->user->id]);
        $project = $this->createProject(['client_id' => $client->id]);
        
        // Create paid billing event
        $this->createEvent([
            'project_id' => $project->id,
            'event_type' => 'billing',
            'amount' => 1500,
            'payment_status' => 'paid'
        ]);
        
        // Create pending billing event
        $this->createEvent([
            'project_id' => $project->id,
            'event_type' => 'billing',
            'amount' => 500,
            'payment_status' => 'pending',
            'payment_due_date' => now()->addDays(5)
        ]);

        $result = $this->repository->paginate(10, []);
        $clientData = $result->items()[0];

        // Check that financial data is included
        $this->assertNotNull($clientData->total_revenue);
        $this->assertNotNull($clientData->pending_amount);
        $this->assertNotNull($clientData->has_overdue_payments);
        
        // Check that financial calculations are working
        $this->assertIsNumeric($clientData->total_revenue);
        $this->assertIsNumeric($clientData->pending_amount);
    }

    public function test_includes_project_counts()
    {
        $client = $this->createClient(['user_id' => $this->user->id]);
        
        $this->createProject(['client_id' => $client->id, 'status' => 'active']);
        $this->createProject(['client_id' => $client->id, 'status' => 'active']);
        $this->createProject(['client_id' => $client->id, 'status' => 'completed']);

        $result = $this->repository->paginate(10, []);
        $clientData = $result->items()[0];

        // Check that project counts are included
        $this->assertNotNull($clientData->projects_count);
        $this->assertNotNull($clientData->active_projects_count);
        $this->assertNotNull($clientData->completed_projects_count);
        
        $this->assertEquals(3, $clientData->projects_count);
        $this->assertEquals(2, $clientData->active_projects_count);
        $this->assertEquals(1, $clientData->completed_projects_count);
    }

    public function test_respects_pagination()
    {
        // Create 15 clients
        for ($i = 1; $i <= 15; $i++) {
            $this->createClient(['user_id' => $this->user->id, 'name' => "Client $i"]);
        }

        $result = $this->repository->paginate(10, []);

        $this->assertEquals(15, $result->total());
        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(2, $result->lastPage());
        $this->assertCount(10, $result->items());
    }

    public function test_handles_empty_results()
    {
        $result = $this->repository->paginate(10, []);

        $this->assertEquals(0, $result->total());
        $this->assertCount(0, $result->items());
    }
}