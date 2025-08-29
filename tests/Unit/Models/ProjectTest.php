<?php

namespace Tests\Unit\Models;

use App\Enums\EventStatus;
use App\Enums\EventType;
use App\Enums\PaymentStatus;
use App\Enums\ProjectStatus;
use App\Models\Client;
use App\Models\Event;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_project()
    {
        $client = Client::factory()->create();
        $projectData = [
            'client_id' => $client->id,
            'name' => 'Test Project',
            'description' => 'Project description',
            'status' => ProjectStatus::Active->value,
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonth(),
            'budget' => 10000.00,
        ];

        $project = Project::create($projectData);

        $this->assertInstanceOf(Project::class, $project);
        $this->assertEquals('Test Project', $project->name);
        $this->assertEquals('Project description', $project->description);
        $this->assertEquals(ProjectStatus::Active->value, $project->status);
        $this->assertEquals(10000.00, $project->budget);
    }

    /** @test */
    public function it_belongs_to_a_client()
    {
        $client = Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->id]);

        $this->assertInstanceOf(Client::class, $project->client);
        $this->assertEquals($client->id, $project->client->id);
    }

    /** @test */
    public function it_has_events_relationship()
    {
        $project = Project::factory()->create();
        $event1 = Event::factory()->step()->create(['project_id' => $project->id]);
        $event2 = Event::factory()->billing()->create(['project_id' => $project->id]);

        $this->assertCount(2, $project->events);
        $this->assertTrue($project->events->contains($event1));
        $this->assertTrue($project->events->contains($event2));
    }

    /** @test */
    public function it_orders_events_by_created_at_desc()
    {
        $project = Project::factory()->create();
        $oldEvent = Event::factory()->create([
            'project_id' => $project->id,
            'created_at' => now()->subDays(2),
        ]);
        $newEvent = Event::factory()->create([
            'project_id' => $project->id,
            'created_at' => now(),
        ]);

        $events = $project->events;
        $this->assertEquals($newEvent->id, $events->first()->id);
        $this->assertEquals($oldEvent->id, $events->last()->id);
    }

    /** @test */
    public function it_can_get_latest_event()
    {
        $project = Project::factory()->create();
        Event::factory()->create([
            'project_id' => $project->id,
            'created_at' => now()->subDays(2),
        ]);
        $latestEvent = Event::factory()->create([
            'project_id' => $project->id,
            'created_at' => now(),
        ]);

        $this->assertEquals($latestEvent->id, $project->latest_event->id);
    }

    /** @test */
    public function it_can_get_events_count()
    {
        $project = Project::factory()->create();
        Event::factory()->count(5)->create(['project_id' => $project->id]);

        $this->assertEquals(5, $project->events_count);
    }

    /** @test */
    public function it_has_active_scope()
    {
        Project::factory()->count(3)->create(['status' => ProjectStatus::Active->value]);
        Project::factory()->count(2)->create(['status' => ProjectStatus::Completed->value]);

        $activeProjects = Project::active()->get();

        $this->assertCount(3, $activeProjects);
        $activeProjects->each(function ($project) {
            $this->assertEquals(ProjectStatus::Active->value, $project->status);
        });
    }

    /** @test */
    public function it_has_completed_scope()
    {
        Project::factory()->count(2)->create(['status' => ProjectStatus::Completed->value]);
        Project::factory()->count(3)->create(['status' => ProjectStatus::Active->value]);

        $completedProjects = Project::completed()->get();

        $this->assertCount(2, $completedProjects);
        $completedProjects->each(function ($project) {
            $this->assertEquals(ProjectStatus::Completed->value, $project->status);
        });
    }

    /** @test */
    public function it_calculates_total_billed_amount()
    {
        $project = Project::factory()->create();
        
        Event::factory()->create([
            'project_id' => $project->id,
            'event_type' => EventType::Billing->value,
            'status' => EventStatus::Sent->value,
            'amount' => 1000,
        ]);
        
        Event::factory()->create([
            'project_id' => $project->id,
            'event_type' => EventType::Billing->value,
            'status' => EventStatus::ToSend->value,
            'amount' => 500,
        ]);
        
        Event::factory()->create([
            'project_id' => $project->id,
            'event_type' => EventType::Billing->value,
            'status' => EventStatus::Cancelled->value,
            'amount' => 300,
        ]);

        $this->assertEquals(1500, $project->total_billed);
    }

    /** @test */
    public function it_calculates_total_paid_amount()
    {
        $project = Project::factory()->create();
        
        Event::factory()->create([
            'project_id' => $project->id,
            'event_type' => EventType::Billing->value,
            'status' => EventStatus::Sent->value,
            'payment_status' => PaymentStatus::Paid->value,
            'amount' => 1000,
        ]);
        
        Event::factory()->create([
            'project_id' => $project->id,
            'event_type' => EventType::Billing->value,
            'status' => EventStatus::Sent->value,
            'payment_status' => PaymentStatus::Pending->value,
            'amount' => 500,
        ]);

        $this->assertEquals(1000, $project->total_paid);
    }

    /** @test */
    public function it_calculates_total_unpaid_amount()
    {
        $project = Project::factory()->create();
        
        Event::factory()->create([
            'project_id' => $project->id,
            'event_type' => EventType::Billing->value,
            'status' => EventStatus::Sent->value,
            'payment_status' => PaymentStatus::Pending->value,
            'amount' => 1500,
        ]);
        
        Event::factory()->create([
            'project_id' => $project->id,
            'event_type' => EventType::Billing->value,
            'status' => EventStatus::Sent->value,
            'payment_status' => PaymentStatus::Paid->value,
            'amount' => 1000,
        ]);

        $this->assertEquals(1500, $project->total_unpaid);
    }

    /** @test */
    public function it_calculates_budget_progress()
    {
        $project = Project::factory()->create(['budget' => 10000]);
        
        Event::factory()->create([
            'project_id' => $project->id,
            'event_type' => EventType::Billing->value,
            'status' => EventStatus::Sent->value,
            'amount' => 2500,
        ]);

        $this->assertEquals(25, $project->budget_progress);
    }

    /** @test */
    public function it_handles_zero_budget_for_progress()
    {
        $project = Project::factory()->create(['budget' => 0]);
        
        Event::factory()->create([
            'project_id' => $project->id,
            'event_type' => EventType::Billing->value,
            'status' => EventStatus::Sent->value,
            'amount' => 1000,
        ]);

        $this->assertEquals(0, $project->budget_progress);
    }

    /** @test */
    public function it_caps_budget_progress_at_100()
    {
        $project = Project::factory()->create(['budget' => 1000]);
        
        Event::factory()->create([
            'project_id' => $project->id,
            'event_type' => EventType::Billing->value,
            'status' => EventStatus::Sent->value,
            'amount' => 2000,
        ]);

        $this->assertEquals(100, $project->budget_progress);
    }

    /** @test */
    public function it_detects_budget_exceeded()
    {
        $project = Project::factory()->create(['budget' => 1000]);
        
        Event::factory()->create([
            'project_id' => $project->id,
            'event_type' => EventType::Billing->value,
            'status' => EventStatus::Sent->value,
            'amount' => 1500,
        ]);

        $this->assertTrue($project->budget_exceeded);
    }

    /** @test */
    public function it_handles_null_budget()
    {
        $project = Project::factory()->create(['budget' => null]);
        
        $this->assertEquals(0, $project->budget_progress);
        $this->assertFalse($project->budget_exceeded);
    }

    /** @test */
    public function it_gets_upcoming_tasks()
    {
        $project = Project::factory()->create();
        
        $upcomingTask = Event::factory()->create([
            'project_id' => $project->id,
            'event_type' => EventType::Step->value,
            'status' => EventStatus::Todo->value,
            'execution_date' => now()->addDays(5),
        ]);
        
        $completedTask = Event::factory()->create([
            'project_id' => $project->id,
            'event_type' => EventType::Step->value,
            'status' => EventStatus::Done->value,
        ]);

        $upcomingTasks = $project->upcomingTasks()->get();

        $this->assertCount(1, $upcomingTasks);
        $this->assertEquals($upcomingTask->id, $upcomingTasks->first()->id);
    }

    /** @test */
    public function it_gets_overdue_tasks()
    {
        $project = Project::factory()->create();
        
        $overdueTask = Event::factory()->create([
            'project_id' => $project->id,
            'event_type' => EventType::Step->value,
            'status' => EventStatus::Todo->value,
            'execution_date' => now()->subDays(5),
        ]);
        
        $normalTask = Event::factory()->create([
            'project_id' => $project->id,
            'event_type' => EventType::Step->value,
            'status' => EventStatus::Todo->value,
            'execution_date' => now()->addDays(5),
        ]);

        $overdueTasks = $project->overdueTasks()->get();

        $this->assertCount(1, $overdueTasks);
        $this->assertEquals($overdueTask->id, $overdueTasks->first()->id);
    }

    /** @test */
    public function it_casts_dates_properly()
    {
        $project = Project::factory()->create();

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $project->start_date);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $project->end_date);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $project->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $project->updated_at);
    }

    /** @test */
    public function it_casts_budget_as_decimal()
    {
        $project = Project::factory()->create(['budget' => 1234.56]);

        $this->assertEquals(1234.56, $project->budget);
        // decimal:2 cast returns a string in Laravel
        $this->assertIsNumeric($project->budget);
    }
}