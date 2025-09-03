<?php

namespace Tests\Unit\Repositories\Projects;

use Tests\TestCase;
use App\Repositories\Projects\ProjectListRepository;
use App\Models\Project;
use App\Models\Client;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;

class ProjectListRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ProjectListRepository $repository;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ProjectListRepository();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_can_paginate_projects()
    {
        $client1 = $this->createClient(['user_id' => $this->user->id]);
        $client2 = $this->createClient(['user_id' => $this->user->id]);
        
        $project1 = $this->createProject(['client_id' => $client1->id, 'name' => 'Project A']);
        $project2 = $this->createProject(['client_id' => $client2->id, 'name' => 'Project B']);
        
        // Create project for another user (should not appear)
        $otherUser = User::factory()->create();
        $otherClient = $this->createClient(['user_id' => $otherUser->id]);
        $this->createProject(['client_id' => $otherClient->id, 'name' => 'Other Project']);

        $result = $this->repository->paginate(10, []);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(2, $result->total());
        $this->assertCount(2, $result->items());
        
        $projectNames = collect($result->items())->pluck('name')->toArray();
        $this->assertContains('Project A', $projectNames);
        $this->assertContains('Project B', $projectNames);
        $this->assertNotContains('Other Project', $projectNames);
    }

    public function test_can_search_projects()
    {
        $client = $this->createClient(['user_id' => $this->user->id, 'name' => 'Acme Corp']);
        
        $project1 = $this->createProject([
            'client_id' => $client->id,
            'name' => 'Website Development',
            'description' => 'Building a new website'
        ]);
        $project2 = $this->createProject([
            'client_id' => $client->id,
            'name' => 'Mobile App',
            'description' => 'Creating mobile application'
        ]);
        
        $otherClient = $this->createClient(['user_id' => $this->user->id, 'name' => 'Tech Solutions']);
        $project3 = $this->createProject([
            'client_id' => $otherClient->id,
            'name' => 'Database Migration',
            'description' => 'Legacy system migration'
        ]);

        // Search by project name
        $result = $this->repository->paginate(10, ['search' => 'Website']);
        $this->assertEquals(1, $result->total());
        $this->assertEquals('Website Development', $result->items()[0]->name);

        // Search by project description
        $result = $this->repository->paginate(10, ['search' => 'mobile']);
        $this->assertEquals(1, $result->total());
        $this->assertEquals('Mobile App', $result->items()[0]->name);

        // Search by client name
        $result = $this->repository->paginate(10, ['search' => 'Acme']);
        $this->assertEquals(2, $result->total());

        // Search by client company
        $result = $this->repository->paginate(10, ['search' => 'Tech Solutions']);
        $this->assertEquals(1, $result->total());
        $this->assertEquals('Database Migration', $result->items()[0]->name);
    }

    public function test_can_filter_projects_by_status()
    {
        $client = $this->createClient(['user_id' => $this->user->id]);
        
        $activeProject = $this->createProject(['client_id' => $client->id, 'status' => 'active']);
        $completedProject = $this->createProject(['client_id' => $client->id, 'status' => 'completed']);
        $onHoldProject = $this->createProject(['client_id' => $client->id, 'status' => 'on_hold']);
        $cancelledProject = $this->createProject(['client_id' => $client->id, 'status' => 'cancelled']);

        // Filter by active status
        $result = $this->repository->paginate(10, ['status' => 'active']);
        $this->assertEquals(1, $result->total());
        $this->assertEquals($activeProject->id, $result->items()[0]->id);

        // Filter by completed status
        $result = $this->repository->paginate(10, ['status' => 'completed']);
        $this->assertEquals(1, $result->total());
        $this->assertEquals($completedProject->id, $result->items()[0]->id);

        // Filter by on_hold status
        $result = $this->repository->paginate(10, ['status' => 'on_hold']);
        $this->assertEquals(1, $result->total());
        $this->assertEquals($onHoldProject->id, $result->items()[0]->id);

        // Filter by cancelled status
        $result = $this->repository->paginate(10, ['status' => 'cancelled']);
        $this->assertEquals(1, $result->total());
        $this->assertEquals($cancelledProject->id, $result->items()[0]->id);
    }

    public function test_can_filter_projects_by_client()
    {
        $client1 = $this->createClient(['user_id' => $this->user->id, 'name' => 'Client 1']);
        $client2 = $this->createClient(['user_id' => $this->user->id, 'name' => 'Client 2']);
        
        $project1 = $this->createProject(['client_id' => $client1->id]);
        $project2 = $this->createProject(['client_id' => $client2->id]);

        $result = $this->repository->paginate(10, ['client_id' => $client1->id]);
        $this->assertEquals(1, $result->total());
        $this->assertEquals($project1->id, $result->items()[0]->id);

        $result = $this->repository->paginate(10, ['client_id' => $client2->id]);
        $this->assertEquals(1, $result->total());
        $this->assertEquals($project2->id, $result->items()[0]->id);
    }

    public function test_can_filter_projects_with_overdue_tasks()
    {
        $client = $this->createClient(['user_id' => $this->user->id]);
        
        $projectWithOverdueTasks = $this->createProject(['client_id' => $client->id]);
        $projectWithoutOverdueTasks = $this->createProject(['client_id' => $client->id]);
        
        // Create overdue step event
        $this->createEvent([
            'project_id' => $projectWithOverdueTasks->id,
            'event_type' => 'step',
            'status' => 'todo',
            'execution_date' => now()->subDays(2)
        ]);
        
        // Create future step event
        $this->createEvent([
            'project_id' => $projectWithoutOverdueTasks->id,
            'event_type' => 'step',
            'status' => 'todo',
            'execution_date' => now()->addDays(2)
        ]);

        $result = $this->repository->paginate(10, ['has_overdue_tasks' => true]);
        $this->assertEquals(1, $result->total());
        $this->assertEquals($projectWithOverdueTasks->id, $result->items()[0]->id);
    }

    public function test_can_filter_projects_with_payment_overdue()
    {
        $client = $this->createClient(['user_id' => $this->user->id]);
        
        $projectWithOverduePayment = $this->createProject(['client_id' => $client->id]);
        $projectWithoutOverduePayment = $this->createProject(['client_id' => $client->id]);
        
        // Create overdue billing event
        $this->createEvent([
            'project_id' => $projectWithOverduePayment->id,
            'event_type' => 'billing',
            'payment_status' => 'pending',
            'payment_due_date' => now()->subDays(5)
        ]);
        
        // Create future billing event
        $this->createEvent([
            'project_id' => $projectWithoutOverduePayment->id,
            'event_type' => 'billing',
            'payment_status' => 'pending',
            'payment_due_date' => now()->addDays(5)
        ]);

        $result = $this->repository->paginate(10, ['has_payment_overdue' => true]);
        $this->assertEquals(1, $result->total());
        $this->assertEquals($projectWithOverduePayment->id, $result->items()[0]->id);
    }

    public function test_can_sort_projects()
    {
        $client = $this->createClient(['user_id' => $this->user->id]);
        
        $project1 = $this->createProject([
            'client_id' => $client->id,
            'name' => 'Alpha Project',
            'created_at' => now()->subDays(2)
        ]);
        $project2 = $this->createProject([
            'client_id' => $client->id,
            'name' => 'Beta Project',
            'created_at' => now()->subDays(1)
        ]);

        // Sort by name ascending
        $result = $this->repository->paginate(10, [
            'sort_by' => 'name',
            'sort_order' => 'asc'
        ]);
        $this->assertEquals('Alpha Project', $result->items()[0]->name);
        $this->assertEquals('Beta Project', $result->items()[1]->name);

        // Sort by name descending
        $result = $this->repository->paginate(10, [
            'sort_by' => 'name',
            'sort_order' => 'desc'
        ]);
        $this->assertEquals('Beta Project', $result->items()[0]->name);
        $this->assertEquals('Alpha Project', $result->items()[1]->name);

        // Sort by created_at (default is desc)
        $result = $this->repository->paginate(10, [
            'sort_by' => 'created_at',
            'sort_order' => 'desc'
        ]);
        $this->assertEquals('Beta Project', $result->items()[0]->name);
    }

    public function test_can_get_global_statistics()
    {
        $client = $this->createClient(['user_id' => $this->user->id]);
        
        $this->createProject(['client_id' => $client->id, 'status' => 'active']);
        $this->createProject(['client_id' => $client->id, 'status' => 'active']);
        $this->createProject(['client_id' => $client->id, 'status' => 'completed']);
        $this->createProject(['client_id' => $client->id, 'status' => 'on_hold']);
        $this->createProject(['client_id' => $client->id, 'status' => 'cancelled']);

        $stats = $this->repository->getGlobalStatistics();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('active', $stats);
        $this->assertArrayHasKey('completed', $stats);
        $this->assertArrayHasKey('on_hold', $stats);
        $this->assertArrayHasKey('cancelled', $stats);
        $this->assertArrayHasKey('total', $stats);
        
        $this->assertEquals(2, $stats['active']);
        $this->assertEquals(1, $stats['completed']);
        $this->assertEquals(1, $stats['on_hold']);
        $this->assertEquals(1, $stats['cancelled']);
        $this->assertEquals(5, $stats['total']);
    }

    public function test_can_get_available_clients()
    {
        $client1 = $this->createClient(['user_id' => $this->user->id, 'name' => 'Client A']);
        $client2 = $this->createClient(['user_id' => $this->user->id, 'name' => 'Client B']);
        
        // Create client for another user (should not appear)
        $otherUser = User::factory()->create();
        $this->createClient(['user_id' => $otherUser->id, 'name' => 'Other Client']);

        $clients = $this->repository->getAvailableClients();

        $this->assertIsArray($clients);
        $this->assertCount(2, $clients);
        
        $clientNames = collect($clients)->pluck('name')->toArray();
        $this->assertContains('Client A', $clientNames);
        $this->assertContains('Client B', $clientNames);
        $this->assertNotContains('Other Client', $clientNames);
    }

    public function test_includes_financial_data()
    {
        $client = $this->createClient(['user_id' => $this->user->id]);
        $project = $this->createProject(['client_id' => $client->id]);
        
        // Create paid billing event
        $this->createEvent([
            'project_id' => $project->id,
            'event_type' => 'billing',
            'amount' => 1234.56, // Unique amount to avoid conflicts
            'payment_status' => 'paid',
            'status' => 'sent'
        ]);
        
        // Create pending billing event
        $this->createEvent([
            'project_id' => $project->id,
            'event_type' => 'billing',
            'amount' => 567.89, // Unique amount to avoid conflicts
            'payment_status' => 'pending',
            'status' => 'sent'
        ]);
        
        // Create cancelled billing event (should not count in total_billed)
        $this->createEvent([
            'project_id' => $project->id,
            'event_type' => 'billing',
            'amount' => 999.99, // Should not affect calculations
            'status' => 'cancelled'
        ]);

        $result = $this->repository->paginate(10, []);
        $projectData = $result->items()[0];

        // Check that financial data is included
        $this->assertNotNull($projectData->total_billed);
        $this->assertNotNull($projectData->total_paid);
        $this->assertNotNull($projectData->total_unpaid);
        
        // Check calculations (cancelled events should not count in total_billed)
        $this->assertEquals('1802.45', $projectData->total_billed); // 1234.56 + 567.89 (cancelled excluded)
        $this->assertEquals('1234.56', $projectData->total_paid);
        $this->assertEquals('567.89', $projectData->total_unpaid);
    }

    public function test_includes_task_counts()
    {
        $client = $this->createClient(['user_id' => $this->user->id]);
        $project = $this->createProject(['client_id' => $client->id]);
        
        $this->createEvent(['project_id' => $project->id, 'event_type' => 'step', 'status' => 'todo']);
        $this->createEvent(['project_id' => $project->id, 'event_type' => 'step', 'status' => 'todo']);
        $this->createEvent(['project_id' => $project->id, 'event_type' => 'step', 'status' => 'done']);
        $this->createEvent(['project_id' => $project->id, 'event_type' => 'billing', 'status' => 'to_send']);

        $result = $this->repository->paginate(10, []);
        $projectData = $result->items()[0];

        // Check that task counts are included
        $this->assertNotNull($projectData->events_count);
        $this->assertNotNull($projectData->completed_tasks_count);
        $this->assertNotNull($projectData->pending_tasks_count);
        
        $this->assertEquals(4, $projectData->events_count); // All events
        $this->assertEquals(1, $projectData->completed_tasks_count); // Only done step events
        $this->assertEquals(2, $projectData->pending_tasks_count); // Only todo step events
    }

    public function test_includes_overdue_indicators()
    {
        $client = $this->createClient(['user_id' => $this->user->id]);
        $project = $this->createProject(['client_id' => $client->id]);
        
        // Create overdue step event
        $this->createEvent([
            'project_id' => $project->id,
            'event_type' => 'step',
            'status' => 'todo',
            'execution_date' => now()->subDays(2)
        ]);
        
        // Create overdue billing payment
        $this->createEvent([
            'project_id' => $project->id,
            'event_type' => 'billing',
            'payment_status' => 'pending',
            'payment_due_date' => now()->subDays(3)
        ]);

        $result = $this->repository->paginate(10, []);
        $projectData = $result->items()[0];

        // Check that overdue indicators are included
        $this->assertNotNull($projectData->has_overdue_events);
        $this->assertNotNull($projectData->has_payment_overdue);
        
        // Both should be true (1) due to overdue events
        $this->assertEquals(1, $projectData->has_overdue_events);
        $this->assertEquals(1, $projectData->has_payment_overdue);
    }

    public function test_respects_pagination()
    {
        $client = $this->createClient(['user_id' => $this->user->id]);
        
        // Create 15 projects
        for ($i = 1; $i <= 15; $i++) {
            $this->createProject(['client_id' => $client->id, 'name' => "Project $i"]);
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

    public function test_includes_client_relationship()
    {
        $client = $this->createClient([
            'user_id' => $this->user->id,
            'name' => 'Test Client',
            'company' => 'Test Company',
            'email' => 'test@example.com'
        ]);
        $project = $this->createProject(['client_id' => $client->id]);

        $result = $this->repository->paginate(10, []);
        $projectData = $result->items()[0];

        // Check that client relationship is loaded
        $this->assertNotNull($projectData->client);
        $this->assertEquals('Test Client', $projectData->client->name);
        $this->assertEquals('Test Company', $projectData->client->company);
        $this->assertEquals('test@example.com', $projectData->client->email);
    }
}