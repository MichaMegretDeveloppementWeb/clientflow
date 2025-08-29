<?php

namespace Tests\Feature;

use App\Enums\EventCategory;
use App\Enums\EventStatus;
use App\Enums\EventType;
use App\Enums\PaymentStatus;
use App\Models\Client;
use App\Models\Event;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $client = Client::factory()->create();
        $this->project = Project::factory()->create(['client_id' => $client->id]);
    }

    /** @test */
    public function it_displays_events_index_page()
    {
        Event::factory()->count(3)->step()->create();
        Event::factory()->count(2)->billing()->create();

        $response = $this->actingAs($this->user)
            ->get(route('events.index'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Events/Index')
                ->has('events.data', 5)
                ->has('events.data.0', fn (Assert $page) => $page
                    ->hasAll(['id', 'name', 'type', 'event_type', 'status', 'project'])
                )
            );
    }

    /** @test */
    public function it_displays_create_event_page()
    {
        Project::factory()->count(3)->create();

        $response = $this->actingAs($this->user)
            ->get(route('events.create'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Events/Create')
                ->has('projects', 4) // 3 created + 1 from setUp
            );
    }

    /** @test */
    public function it_can_store_a_step_event()
    {
        $eventData = [
            'project_id' => $this->project->id,
            'name' => 'Réunion de lancement',
            'description' => 'Première réunion du projet',
            'type' => EventCategory::Meeting->value,
            'event_type' => EventType::Step->value,
            'status' => EventStatus::Todo->value,
            'created_date' => now()->format('Y-m-d'),
            'execution_date' => now()->addDays(5)->format('Y-m-d')
        ];

        $response = $this->actingAs($this->user)
            ->post(route('events.store'), $eventData);

        $response->assertRedirect(route('events.index'));
        $response->assertSessionHas('message', 'Événement créé avec succès.');
        
        $this->assertDatabaseHas('events', [
            'name' => 'Réunion de lancement',
            'event_type' => EventType::Step->value,
            'status' => EventStatus::Todo->value
        ]);
    }

    /** @test */
    public function it_can_store_a_billing_event()
    {
        $eventData = [
            'project_id' => $this->project->id,
            'name' => 'Facture #001',
            'description' => 'Première facture',
            'type' => EventCategory::Invoice->value,
            'event_type' => EventType::Billing->value,
            'status' => EventStatus::ToSend->value,
            'amount' => 5000,
            'payment_status' => PaymentStatus::Pending->value,
            'created_date' => now()->format('Y-m-d'),
            'send_date' => now()->addDays(2)->format('Y-m-d'),
            'payment_due_date' => now()->addDays(30)->format('Y-m-d')
        ];

        $response = $this->actingAs($this->user)
            ->post(route('events.store'), $eventData);

        $response->assertRedirect(route('events.index'));
        
        $this->assertDatabaseHas('events', [
            'name' => 'Facture #001',
            'event_type' => EventType::Billing->value,
            'amount' => 5000,
            'payment_status' => PaymentStatus::Pending->value
        ]);
    }

    /** @test */
    public function it_validates_required_fields_for_step_event()
    {
        $eventData = [
            'event_type' => EventType::Step->value
        ];

        $response = $this->actingAs($this->user)
            ->post(route('events.store'), $eventData);

        $response->assertSessionHasErrors(['project_id', 'name', 'type', 'status']);
    }

    /** @test */
    public function it_validates_required_fields_for_billing_event()
    {
        $eventData = [
            'event_type' => EventType::Billing->value
        ];

        $response = $this->actingAs($this->user)
            ->post(route('events.store'), $eventData);

        $response->assertSessionHasErrors(['project_id', 'name', 'type', 'status', 'amount']);
    }

    /** @test */
    public function it_displays_event_details()
    {
        $event = Event::factory()->step()->create();

        $response = $this->actingAs($this->user)
            ->get(route('events.show', $event));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Events/Show')
                ->has('event', fn (Assert $page) => $page
                    ->where('id', $event->id)
                    ->hasAll(['name', 'description', 'type', 'event_type', 'status', 'project'])
                )
            );
    }

    /** @test */
    public function it_displays_edit_event_page()
    {
        $event = Event::factory()->create();
        Project::factory()->count(2)->create();

        $response = $this->actingAs($this->user)
            ->get(route('events.edit', $event));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Events/Edit')
                ->has('event', fn (Assert $page) => $page
                    ->where('id', $event->id)
                    ->hasAll(['name', 'description', 'type', 'event_type', 'status'])
                )
                ->has('projects', 3) // 2 created + 1 from setUp
            );
    }

    /** @test */
    public function it_can_update_event_status()
    {
        $event = Event::factory()->step()->create([
            'status' => EventStatus::Todo->value
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('events.updateStatus', $event), [
                'status' => EventStatus::Done->value
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'status' => EventStatus::Done->value
        ]);
        
        // Check that completed_at was set
        $this->assertNotNull($event->fresh()->completed_at);
    }

    /** @test */
    public function it_can_update_payment_status()
    {
        $event = Event::factory()->billing()->create([
            'status' => EventStatus::Sent->value,
            'payment_status' => PaymentStatus::Pending->value
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('events.updateStatus', $event), [
                'payment_status' => PaymentStatus::Paid->value,
                'paid_at' => now()->format('Y-m-d')
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'payment_status' => PaymentStatus::Paid->value
        ]);
        
        $this->assertNotNull($event->fresh()->paid_at);
    }

    /** @test */
    public function it_can_delete_an_event()
    {
        $event = Event::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('events.destroy', $event));

        $response->assertRedirect(route('events.index'));
        
        $this->assertDatabaseMissing('events', [
            'id' => $event->id
        ]);
    }

    /** @test */
    public function it_filters_events_by_type()
    {
        Event::factory()->count(3)->step()->create();
        Event::factory()->count(2)->billing()->create();

        $response = $this->actingAs($this->user)
            ->get(route('events.index', ['event_type' => 'billing']));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Events/Index')
                ->has('events.data', 2)
                ->has('events.data.0', fn (Assert $page) => $page
                    ->where('event_type', EventType::Billing->value)
                )
            );
    }

    /** @test */
    public function it_filters_events_by_status()
    {
        Event::factory()->count(2)->create(['status' => EventStatus::Todo->value]);
        Event::factory()->count(3)->create(['status' => EventStatus::Done->value]);

        $response = $this->actingAs($this->user)
            ->get(route('events.index', ['status' => 'todo']));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Events/Index')
                ->has('events.data', 2)
            );
    }

    /** @test */
    public function it_shows_overdue_events()
    {
        // Create overdue step event
        Event::factory()->step()->create([
            'status' => EventStatus::Todo->value,
            'execution_date' => now()->subDays(5)
        ]);
        
        // Create overdue billing event
        Event::factory()->billing()->create([
            'status' => EventStatus::ToSend->value,
            'send_date' => now()->subDays(3)
        ]);
        
        // Create normal event
        Event::factory()->step()->create([
            'status' => EventStatus::Todo->value,
            'execution_date' => now()->addDays(5)
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('events.index', ['overdue' => true]));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Events/Index')
                ->has('events.data', 2)
            );
    }

    /** @test */
    public function it_shows_unpaid_billing_events()
    {
        Event::factory()->billing()->create([
            'status' => EventStatus::Sent->value,
            'payment_status' => PaymentStatus::Pending->value
        ]);
        
        Event::factory()->billing()->create([
            'status' => EventStatus::Sent->value,
            'payment_status' => PaymentStatus::Paid->value
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('events.index', ['unpaid' => true]));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Events/Index')
                ->has('events.data', 1)
            );
    }

    /** @test */
    public function it_gets_events_for_specific_project()
    {
        $project = Project::factory()->create();
        Event::factory()->count(3)->create(['project_id' => $project->id]);
        Event::factory()->count(2)->create(); // Other project

        $response = $this->actingAs($this->user)
            ->get(route('projects.events', $project));

        $response->assertOk()
            ->assertJson([
                'events' => [
                    'data' => []
                ]
            ]);
        
        $this->assertCount(3, $response->json('events.data'));
    }

    /** @test */
    public function it_validates_dates_when_creating_billing_event()
    {
        $eventData = [
            'project_id' => $this->project->id,
            'name' => 'Test Invoice',
            'type' => EventCategory::Invoice->value,
            'event_type' => EventType::Billing->value,
            'status' => EventStatus::ToSend->value,
            'amount' => 1000,
            'created_date' => now()->format('Y-m-d'),
            'send_date' => now()->subDays(5)->format('Y-m-d'), // Send date before created date
            'payment_due_date' => now()->addDays(30)->format('Y-m-d')
        ];

        $response = $this->actingAs($this->user)
            ->post(route('events.store'), $eventData);

        $response->assertSessionHasErrors(['send_date']);
    }

    /** @test */
    public function it_requires_authentication_to_access_events()
    {
        $response = $this->get(route('events.index'));
        $response->assertRedirect(route('login'));

        $event = Event::factory()->create();
        $response = $this->get(route('events.show', $event));
        $response->assertRedirect(route('login'));
    }
}