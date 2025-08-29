<?php

namespace Tests\Feature;

use App\Enums\EventStatus;
use App\Enums\EventType;
use App\Enums\PaymentStatus;
use App\Enums\ProjectStatus;
use App\Models\Client;
use App\Models\Event;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->client = Client::factory()->create();
    }

    /** @test */
    public function it_displays_projects_index_page()
    {
        Project::factory()->count(5)->create();

        $response = $this->actingAs($this->user)
            ->get(route('projects.index'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Projects/Index')
                ->has('projects.data', 5)
                ->has('projects.data.0', fn (Assert $page) => $page
                    ->hasAll(['id', 'name', 'status', 'client', 'budget', 'start_date', 'end_date'])
                )
            );
    }

    /** @test */
    public function it_displays_create_project_page()
    {
        Client::factory()->count(3)->create();

        $response = $this->actingAs($this->user)
            ->get(route('projects.create'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Projects/Create')
                ->has('clients', 4) // 3 created + 1 from setUp
            );
    }

    /** @test */
    public function it_can_store_a_new_project()
    {
        $projectData = [
            'client_id' => $this->client->id,
            'name' => 'Nouveau Projet Web',
            'description' => 'Description du projet',
            'status' => ProjectStatus::Active->value,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addMonths(3)->format('Y-m-d'),
            'budget' => 25000
        ];

        $response = $this->actingAs($this->user)
            ->post(route('projects.store'), $projectData);

        $response->assertRedirect(route('projects.index'));
        $response->assertSessionHas('message', 'Projet créé avec succès.');
        
        $this->assertDatabaseHas('projects', [
            'name' => 'Nouveau Projet Web',
            'client_id' => $this->client->id,
            'budget' => 25000
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_project()
    {
        $response = $this->actingAs($this->user)
            ->post(route('projects.store'), []);

        $response->assertSessionHasErrors(['client_id', 'name', 'status']);
    }

    /** @test */
    public function it_validates_date_order()
    {
        $projectData = [
            'client_id' => $this->client->id,
            'name' => 'Test Project',
            'status' => ProjectStatus::Active->value,
            'start_date' => now()->addMonth()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'), // End before start
            'budget' => 10000
        ];

        $response = $this->actingAs($this->user)
            ->post(route('projects.store'), $projectData);

        $response->assertSessionHasErrors(['end_date']);
    }

    /** @test */
    public function it_displays_project_details_with_events()
    {
        $project = Project::factory()->create();
        Event::factory()->count(3)->step()->create(['project_id' => $project->id]);
        Event::factory()->count(2)->billing()->create(['project_id' => $project->id]);

        $response = $this->actingAs($this->user)
            ->get(route('projects.show', $project));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Projects/Show')
                ->has('project', fn (Assert $page) => $page
                    ->where('id', $project->id)
                    ->hasAll(['name', 'description', 'status', 'budget', 'client'])
                )
                ->has('events', 5)
                ->has('stats')
            );
    }

    /** @test */
    public function it_displays_edit_project_page()
    {
        $project = Project::factory()->create();
        Client::factory()->count(2)->create();

        $response = $this->actingAs($this->user)
            ->get(route('projects.edit', $project));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Projects/Edit')
                ->has('project', fn (Assert $page) => $page
                    ->where('id', $project->id)
                    ->hasAll(['name', 'description', 'status', 'budget', 'client_id'])
                )
                ->has('clients', 3) // 2 created + 1 from setUp
            );
    }

    /** @test */
    public function it_can_update_a_project()
    {
        $project = Project::factory()->create([
            'name' => 'Old Project Name',
            'status' => ProjectStatus::Active->value
        ]);

        $updatedData = [
            'client_id' => $project->client_id,
            'name' => 'Updated Project Name',
            'description' => 'Updated description',
            'status' => ProjectStatus::Completed->value,
            'start_date' => $project->start_date->format('Y-m-d'),
            'end_date' => $project->end_date->format('Y-m-d'),
            'budget' => 35000
        ];

        $response = $this->actingAs($this->user)
            ->put(route('projects.update', $project), $updatedData);

        $response->assertRedirect(route('projects.index'));
        $response->assertSessionHas('message', 'Projet mis à jour avec succès.');
        
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Project Name',
            'status' => ProjectStatus::Completed->value,
            'budget' => 35000
        ]);
    }

    /** @test */
    public function it_can_delete_a_project()
    {
        $project = Project::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('projects.destroy', $project));

        $response->assertRedirect(route('projects.index'));
        $response->assertSessionHas('message', 'Projet supprimé avec succès.');
        
        $this->assertDatabaseMissing('projects', [
            'id' => $project->id
        ]);
    }

    /** @test */
    public function it_filters_projects_by_status()
    {
        Project::factory()->count(3)->create(['status' => ProjectStatus::Active->value]);
        Project::factory()->count(2)->create(['status' => ProjectStatus::Completed->value]);
        Project::factory()->create(['status' => ProjectStatus::OnHold->value]);

        $response = $this->actingAs($this->user)
            ->get(route('projects.index', ['status' => 'active']));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Projects/Index')
                ->has('projects.data', 3)
                ->has('projects.data.0', fn (Assert $page) => $page
                    ->where('status', ProjectStatus::Active->value)
                )
            );
    }

    /** @test */
    public function it_calculates_project_financial_statistics()
    {
        $project = Project::factory()->create(['budget' => 10000]);
        
        // Create billing events
        Event::factory()->create([
            'project_id' => $project->id,
            'event_type' => EventType::Billing->value,
            'status' => EventStatus::Sent->value,
            'amount' => 3000,
            'payment_status' => PaymentStatus::Paid->value
        ]);
        
        Event::factory()->create([
            'project_id' => $project->id,
            'event_type' => EventType::Billing->value,
            'status' => EventStatus::Sent->value,
            'amount' => 2000,
            'payment_status' => PaymentStatus::Pending->value
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('projects.show', $project));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Projects/Show')
                ->where('stats.total_billed', 5000)
                ->where('stats.total_paid', 3000)
                ->where('stats.total_pending', 2000)
                ->where('stats.budget_used_percentage', 50)
            );
    }

    /** @test */
    public function it_shows_overdue_projects()
    {
        // Create an overdue project
        Project::factory()->create([
            'status' => ProjectStatus::Active->value,
            'end_date' => now()->subDays(5)
        ]);
        
        // Create a normal project
        Project::factory()->create([
            'status' => ProjectStatus::Active->value,
            'end_date' => now()->addDays(5)
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('projects.index', ['overdue' => true]));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Projects/Index')
                ->has('projects.data', 1)
            );
    }

    /** @test */
    public function it_requires_authentication_to_access_projects()
    {
        $response = $this->get(route('projects.index'));
        $response->assertRedirect(route('login'));

        $project = Project::factory()->create();
        $response = $this->get(route('projects.show', $project));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_validates_budget_is_numeric()
    {
        $projectData = [
            'client_id' => $this->client->id,
            'name' => 'Test Project',
            'status' => ProjectStatus::Active->value,
            'budget' => 'not-a-number'
        ];

        $response = $this->actingAs($this->user)
            ->post(route('projects.store'), $projectData);

        $response->assertSessionHasErrors(['budget']);
    }

    /** @test */
    public function it_validates_status_is_valid_enum()
    {
        $projectData = [
            'client_id' => $this->client->id,
            'name' => 'Test Project',
            'status' => 'invalid-status'
        ];

        $response = $this->actingAs($this->user)
            ->post(route('projects.store'), $projectData);

        $response->assertSessionHasErrors(['status']);
    }
}