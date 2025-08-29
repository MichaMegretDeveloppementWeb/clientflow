<?php

namespace Database\Seeders;

use App\Enums\EventStatus;
use App\Enums\EventType;
use App\Enums\PaymentStatus;
use App\Enums\ProjectStatus;
use App\Models\Client;
use App\Models\Event;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Configuration for data distribution
     */
    private const CLIENT_COUNT = 20;
    private const PROJECT_COUNT = 100;
    private const EVENT_COUNT = 500;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Start transaction for data integrity
        DB::beginTransaction();

        try {
            $this->command->info('🚀 Starting database seeding...');

            // Create default user for testing
            $this->seedUsers();

            // Create clients with realistic distribution
            $clients = $this->seedClients();

            // Create projects distributed among clients
            $projects = $this->seedProjects($clients);

            // Create events distributed among projects
            $this->seedEvents($projects);

            // Commit transaction
            DB::commit();

            // Display statistics
            $this->displayStatistics();

            $this->command->info('✅ Database seeding completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Seeding failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Seed users
     */
    private function seedUsers(): void
    {
        $this->command->info('Creating users...');

        // Create or update test user
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        // Create or update admin user
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );

        // Create additional users if needed
        if (User::count() < 5) {
            User::factory(5 - User::count())->create();
        }

        $this->command->info('✓ Users created');
    }

    /**
     * Seed clients with realistic distribution
     */
    private function seedClients(): \Illuminate\Support\Collection
    {
        $this->command->info('Creating ' . self::CLIENT_COUNT . ' clients...');

        $clients = collect();

        // Create mix of company and individual clients
        // 70% companies, 30% individuals
        $companyCount = (int) (self::CLIENT_COUNT * 0.7);
        $individualCount = self::CLIENT_COUNT - $companyCount;

        // Create company clients
        $clients = $clients->merge(
            Client::factory()
                ->count($companyCount)
                ->company()
                ->create()
        );

        // Create individual clients
        $clients = $clients->merge(
            Client::factory()
                ->count($individualCount)
                ->individual()
                ->create()
        );

        // Add detailed notes to some important clients (20%)
        $importantClients = $clients->random((int) (self::CLIENT_COUNT * 0.2));
        foreach ($importantClients as $client) {
            $client->update([
                'notes' => "Client important - Contrat cadre en cours.\n" . fake('fr_FR')->text(100)
            ]);
        }

        $this->command->info('✓ Clients created');

        return $clients;
    }

    /**
     * Seed projects with realistic distribution among clients
     */
    private function seedProjects($clients): \Illuminate\Support\Collection
    {
        $this->command->info('Creating ' . self::PROJECT_COUNT . ' projects...');

        $projects = collect();
        $projectsPerClient = [];

        // Calculate project distribution
        // Some clients have many projects (big clients), others have few
        foreach ($clients as $index => $client) {
            if ($index < 3) {
                // Top 3 clients get 10-15 projects each (big clients)
                $projectsPerClient[$client->id] = rand(10, 15);
            } elseif ($index < 8) {
                // Next 5 clients get 5-8 projects (medium clients)
                $projectsPerClient[$client->id] = rand(5, 8);
            } else {
                // Remaining clients get 1-3 projects (small clients)
                $projectsPerClient[$client->id] = rand(1, 3);
            }
        }

        // Adjust to match exact count
        $totalProjects = array_sum($projectsPerClient);
        if ($totalProjects < self::PROJECT_COUNT) {
            // Add remaining projects to top clients
            $remaining = self::PROJECT_COUNT - $totalProjects;
            $topClients = array_slice(array_keys($projectsPerClient), 0, 3);
            foreach ($topClients as $clientId) {
                $projectsPerClient[$clientId] += intval($remaining / 3);
                $remaining -= intval($remaining / 3);
            }
            if ($remaining > 0) {
                $projectsPerClient[$topClients[0]] += $remaining;
            }
        } elseif ($totalProjects > self::PROJECT_COUNT) {
            // Remove excess projects from small clients
            $excess = $totalProjects - self::PROJECT_COUNT;
            $smallClients = array_slice(array_keys($projectsPerClient), -5);
            foreach ($smallClients as $clientId) {
                if ($excess <= 0) break;
                if ($projectsPerClient[$clientId] > 1) {
                    $projectsPerClient[$clientId]--;
                    $excess--;
                }
            }
        }

        // Create projects for each client
        foreach ($projectsPerClient as $clientId => $count) {
            for ($i = 0; $i < $count; $i++) {
                // Determine project status distribution
                $statusRand = rand(1, 100);
                if ($statusRand <= 40) {
                    // 40% active projects
                    $project = Project::factory()->active()->create(['client_id' => $clientId]);
                } elseif ($statusRand <= 70) {
                    // 30% completed projects
                    $project = Project::factory()->completed()->create(['client_id' => $clientId]);
                } elseif ($statusRand <= 85) {
                    // 15% on hold
                    $project = Project::factory()->onHold()->create(['client_id' => $clientId]);
                } else {
                    // 15% cancelled
                    $project = Project::factory()->cancelled()->create(['client_id' => $clientId]);
                }

                $projects->push($project);
            }
        }

        $this->command->info('✓ Projects created');

        return $projects;
    }

    /**
     * Seed events with realistic distribution among projects
     */
    private function seedEvents($projects): void
    {
        $this->command->info('Creating ' . self::EVENT_COUNT . ' events...');

        $eventsCreated = 0;
        $activeProjects = $projects->where('status', ProjectStatus::Active->value);
        $completedProjects = $projects->where('status', ProjectStatus::Completed->value);
        $otherProjects = $projects->whereNotIn('status', [ProjectStatus::Active->value, ProjectStatus::Completed->value]);

        // Active projects get more events (5-10 each)
        foreach ($activeProjects as $project) {
            $eventCount = rand(5, 10);
            $this->createEventsForProject($project, $eventCount);
            $eventsCreated += $eventCount;
        }

        // Completed projects get moderate events (3-7 each)
        foreach ($completedProjects as $project) {
            $eventCount = rand(3, 7);
            $this->createEventsForProject($project, $eventCount);
            $eventsCreated += $eventCount;
        }

        // Other projects get fewer events (1-3 each)
        foreach ($otherProjects as $project) {
            $eventCount = rand(1, 3);
            $this->createEventsForProject($project, $eventCount);
            $eventsCreated += $eventCount;
        }

        // If we haven't created enough events, add more to active projects
        while ($eventsCreated < self::EVENT_COUNT) {
            $project = $activeProjects->random();
            $this->createEventsForProject($project, 1);
            $eventsCreated++;
        }

        $this->command->info('✓ Events created');
    }

    /**
     * Create events for a specific project
     */
    private function createEventsForProject(Project $project, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            // 60% step events, 40% billing events
            if (rand(1, 100) <= 60) {
                $this->createStepEvent($project);
            } else {
                $this->createBillingEvent($project);
            }
        }
    }

    /**
     * Create a step event for a project
     */
    private function createStepEvent(Project $project): void
    {
        $statusRand = rand(1, 100);

        if ($project->status === ProjectStatus::Active->value) {
            // Active projects: mix of todo and done
            if ($statusRand <= 50) {
                Event::factory()->step()->pending()->create(['project_id' => $project->id]);
            } else {
                Event::factory()->step()->completed()->create(['project_id' => $project->id]);
            }
        } elseif ($project->status === ProjectStatus::Completed->value) {
            // Completed projects: mostly done
            if ($statusRand <= 80) {
                Event::factory()->step()->completed()->create(['project_id' => $project->id]);
            } else {
                Event::factory()->step()->create(['project_id' => $project->id]);
            }
        } else {
            // Other projects: mixed
            Event::factory()->step()->create(['project_id' => $project->id]);
        }
    }

    /**
     * Create a billing event for a project
     */
    private function createBillingEvent(Project $project): void
    {
        $statusRand = rand(1, 100);

        if ($project->status === ProjectStatus::Active->value) {
            // Active projects: mix of states
            if ($statusRand <= 30) {
                Event::factory()->billing()->pending()->create(['project_id' => $project->id]);
            } elseif ($statusRand <= 70) {
                Event::factory()->billing()->completed()->create(['project_id' => $project->id]);
            } else {
                Event::factory()->billing()->paid()->create(['project_id' => $project->id]);
            }
        } elseif ($project->status === ProjectStatus::Completed->value) {
            // Completed projects: mostly paid
            if ($statusRand <= 70) {
                Event::factory()->billing()->paid()->create(['project_id' => $project->id]);
            } else {
                Event::factory()->billing()->completed()->create(['project_id' => $project->id]);
            }
        } else {
            // Other projects: various states
            Event::factory()->billing()->create(['project_id' => $project->id]);
        }
    }

    /**
     * Display seeding statistics
     */
    private function displayStatistics(): void
    {
        $this->command->info("\n📊 Seeding Statistics:");
        $this->command->table(
            ['Entity', 'Count'],
            [
                ['Users', User::count()],
                ['Clients', Client::count()],
                ['Projects', Project::count()],
                ['Events', Event::count()],
            ]
        );

        $this->command->info("\n📈 Project Status Distribution:");
        $projectStats = Project::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $this->command->table(
            ['Status', 'Count'],
            collect($projectStats)->map(fn($count, $status) => [
                ProjectStatus::from($status)->label(),
                $count
            ])->toArray()
        );

        $this->command->info("\n📋 Event Type Distribution:");
        $eventStats = Event::withoutGlobalScope('ordered')
            ->selectRaw('event_type, count(*) as count')
            ->groupBy('event_type')
            ->pluck('count', 'event_type')
            ->toArray();

        $this->command->table(
            ['Type', 'Count'],
            collect($eventStats)->map(fn($count, $type) => [
                EventType::from($type)->label(),
                $count
            ])->toArray()
        );

        // Financial statistics
        $totalRevenue = Event::where('event_type', EventType::Billing->value)
            ->where('payment_status', PaymentStatus::Paid->value)
            ->sum('amount');

        $pendingRevenue = Event::where('event_type', EventType::Billing->value)
            ->where('payment_status', PaymentStatus::Pending->value)
            ->sum('amount');

        $this->command->info("\n💰 Financial Overview:");
        $this->command->table(
            ['Metric', 'Amount'],
            [
                ['Total Paid Revenue', number_format($totalRevenue, 2) . ' €'],
                ['Pending Revenue', number_format($pendingRevenue, 2) . ' €'],
                ['Total Potential', number_format($totalRevenue + $pendingRevenue, 2) . ' €'],
            ]
        );
    }
}
