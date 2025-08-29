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

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_displays_dashboard_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')
                ->hasAll([
                    'stats',
                    'urgentTasks',
                    'upcomingEvents',
                    'recentProjects',
                    'revenueData'
                ])
            );
    }

    /** @test */
    public function it_calculates_dashboard_statistics_correctly()
    {
        // Create test data
        Client::factory()->count(5)->create();
        Project::factory()->count(3)->create(['status' => ProjectStatus::Active->value]);
        Project::factory()->count(2)->create(['status' => ProjectStatus::Completed->value]);
        
        // Create events
        Event::factory()->count(4)->step()->create(['status' => EventStatus::Todo->value]);
        Event::factory()->count(2)->billing()->create([
            'status' => EventStatus::Sent->value,
            'payment_status' => PaymentStatus::Pending->value,
            'amount' => 1000
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')
                ->where('stats.total_clients', 5)
                ->where('stats.active_projects', 3)
                ->where('stats.pending_tasks', 4)
                ->where('stats.unpaid_invoices', 2)
            );
    }

    /** @test */
    public function it_shows_urgent_tasks()
    {
        // Create overdue step event
        $overdueStep = Event::factory()->step()->create([
            'name' => 'Overdue Task',
            'status' => EventStatus::Todo->value,
            'execution_date' => now()->subDays(3)
        ]);
        
        // Create overdue billing event
        $overdueBilling = Event::factory()->billing()->create([
            'name' => 'Overdue Invoice',
            'status' => EventStatus::ToSend->value,
            'send_date' => now()->subDays(2)
        ]);
        
        // Create normal event (not urgent)
        Event::factory()->step()->create([
            'status' => EventStatus::Todo->value,
            'execution_date' => now()->addDays(5)
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')
                ->has('urgentTasks', 2)
                ->has('urgentTasks.0', fn (Assert $page) => $page
                    ->hasAll(['id', 'name', 'type', 'event_type', 'status', 'project'])
                )
            );
    }

    /** @test */
    public function it_shows_upcoming_events()
    {
        // Create upcoming events
        Event::factory()->step()->create([
            'name' => 'Meeting Tomorrow',
            'status' => EventStatus::Todo->value,
            'execution_date' => now()->addDay()
        ]);
        
        Event::factory()->billing()->create([
            'name' => 'Invoice Next Week',
            'status' => EventStatus::ToSend->value,
            'send_date' => now()->addWeek()
        ]);
        
        // Create past event (not upcoming)
        Event::factory()->step()->create([
            'status' => EventStatus::Done->value,
            'execution_date' => now()->subDays(5)
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')
                ->has('upcomingEvents', 2)
            );
    }

    /** @test */
    public function it_shows_recent_projects()
    {
        // Create projects with different created dates
        $recentProject = Project::factory()->create([
            'created_at' => now()->subDay()
        ]);
        
        $olderProject = Project::factory()->create([
            'created_at' => now()->subMonths(2)
        ]);
        
        Project::factory()->create([
            'created_at' => now()->subWeek()
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')
                ->has('recentProjects', 3)
                ->where('recentProjects.0.id', $recentProject->id) // Most recent first
            );
    }

    /** @test */
    public function it_calculates_revenue_data()
    {
        // Create paid invoices for different months
        Event::factory()->billing()->create([
            'status' => EventStatus::Sent->value,
            'payment_status' => PaymentStatus::Paid->value,
            'amount' => 5000,
            'paid_at' => now()->subMonths(2)
        ]);
        
        Event::factory()->billing()->create([
            'status' => EventStatus::Sent->value,
            'payment_status' => PaymentStatus::Paid->value,
            'amount' => 3000,
            'paid_at' => now()->subMonth()
        ]);
        
        Event::factory()->billing()->create([
            'status' => EventStatus::Sent->value,
            'payment_status' => PaymentStatus::Paid->value,
            'amount' => 7000,
            'paid_at' => now()
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')
                ->has('revenueData')
                ->has('revenueData.labels')
                ->has('revenueData.datasets')
            );
    }

    /** @test */
    public function it_shows_payment_overdue_invoices()
    {
        // Create overdue payment
        Event::factory()->billing()->create([
            'status' => EventStatus::Sent->value,
            'payment_status' => PaymentStatus::Pending->value,
            'payment_due_date' => now()->subDays(5),
            'amount' => 2000
        ]);
        
        // Create normal pending payment
        Event::factory()->billing()->create([
            'status' => EventStatus::Sent->value,
            'payment_status' => PaymentStatus::Pending->value,
            'payment_due_date' => now()->addDays(15),
            'amount' => 1500
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')
                ->where('stats.overdue_payments_amount', 2000)
            );
    }

    /** @test */
    public function it_calculates_monthly_revenue_statistics()
    {
        // Create invoices for current month
        Event::factory()->billing()->create([
            'status' => EventStatus::Sent->value,
            'payment_status' => PaymentStatus::Paid->value,
            'amount' => 5000,
            'paid_at' => now()->startOfMonth()->addDays(5)
        ]);
        
        Event::factory()->billing()->create([
            'status' => EventStatus::Sent->value,
            'payment_status' => PaymentStatus::Paid->value,
            'amount' => 3000,
            'paid_at' => now()->startOfMonth()->addDays(15)
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')
                ->where('stats.current_month_revenue', 8000)
            );
    }

    /** @test */
    public function it_limits_number_of_urgent_tasks()
    {
        // Create many overdue events
        Event::factory()->count(15)->step()->create([
            'status' => EventStatus::Todo->value,
            'execution_date' => now()->subDays(5)
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')
                ->has('urgentTasks', 10) // Should be limited to 10
            );
    }

    /** @test */
    public function it_requires_authentication_to_access_dashboard()
    {
        $response = $this->get(route('dashboard'));
        
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_shows_projects_by_status_distribution()
    {
        Project::factory()->count(5)->create(['status' => ProjectStatus::Active->value]);
        Project::factory()->count(3)->create(['status' => ProjectStatus::Completed->value]);
        Project::factory()->count(2)->create(['status' => ProjectStatus::OnHold->value]);
        Project::factory()->create(['status' => ProjectStatus::Cancelled->value]);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')
                ->where('stats.projects_by_status.active', 5)
                ->where('stats.projects_by_status.completed', 3)
                ->where('stats.projects_by_status.on_hold', 2)
                ->where('stats.projects_by_status.cancelled', 1)
            );
    }

    /** @test */
    public function it_handles_empty_dashboard_gracefully()
    {
        // No data created - empty database

        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')
                ->where('stats.total_clients', 0)
                ->where('stats.active_projects', 0)
                ->where('stats.pending_tasks', 0)
                ->where('stats.unpaid_invoices', 0)
                ->has('urgentTasks', 0)
                ->has('upcomingEvents', 0)
                ->has('recentProjects', 0)
            );
    }
}