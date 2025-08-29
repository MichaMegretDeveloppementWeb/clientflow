<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use App\Models\Event;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ApplicationEditPagesTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
    }

    public function test_client_edit_page_loads(): void
    {
        $client = Client::factory()->create();
        
        $response = $this->actingAs($this->user)->get("/clients/{$client->id}/edit");
        
        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page->component('Clients/Edit'));
    }

    public function test_project_edit_page_loads(): void
    {
        $client = Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->id]);
        
        $response = $this->actingAs($this->user)->get("/projects/{$project->id}/edit");
        
        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page->component('Projects/Edit'));
    }

    public function test_event_edit_page_loads(): void
    {
        $client = Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->id]);
        $event = Event::factory()->create([
            'project_id' => $project->id,
            'event_type' => 'step',
            'created_date' => now(),
            'execution_date' => now()->addDays(7)
        ]);
        
        $response = $this->actingAs($this->user)->get("/events/{$event->id}/edit");
        
        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page->component('Events/Edit'));
    }
}