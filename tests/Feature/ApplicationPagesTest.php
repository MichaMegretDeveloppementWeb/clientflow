<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use App\Models\Event;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ApplicationPagesTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
    }

    public function test_dashboard_page_loads(): void
    {
        $response = $this->actingAs($this->user)->get('/dashboard');
        
        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page->component('Dashboard/Index'));
    }

    public function test_clients_index_page_loads(): void
    {
        $response = $this->actingAs($this->user)->get('/clients');
        
        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page->component('Clients/Index'));
    }

    public function test_clients_create_page_loads(): void
    {
        $response = $this->actingAs($this->user)->get('/clients/create');
        
        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page->component('Clients/Create'));
    }

    public function test_projects_index_page_loads(): void
    {
        $response = $this->actingAs($this->user)->get('/projects');
        
        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page->component('Projects/Index'));
    }

    public function test_projects_create_page_loads(): void
    {
        $response = $this->actingAs($this->user)->get('/projects/create');
        
        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page->component('Projects/Create'));
    }

    public function test_events_index_page_loads(): void
    {
        $response = $this->actingAs($this->user)->get('/events');
        
        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page->component('Events/Index'));
    }

    public function test_events_create_page_loads(): void
    {
        $response = $this->actingAs($this->user)->get('/events/create');
        
        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page->component('Events/Create'));
    }

    public function test_client_show_page_loads(): void
    {
        $client = Client::factory()->create();
        
        $response = $this->actingAs($this->user)->get("/clients/{$client->id}");
        
        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page->component('Clients/Show'));
    }

    public function test_project_show_page_loads(): void
    {
        $client = Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->id]);
        
        $response = $this->actingAs($this->user)->get("/projects/{$project->id}");
        
        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page->component('Projects/Show'));
    }

    public function test_event_show_page_loads(): void
    {
        $client = Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->id]);
        $event = Event::factory()->create([
            'project_id' => $project->id,
            'event_type' => 'step',
            'created_date' => now(),
            'execution_date' => now()->addDays(7)
        ]);
        
        $response = $this->actingAs($this->user)->get("/events/{$event->id}");
        
        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page->component('Events/Show'));
    }
}