<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Project;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un utilisateur de test
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Créer quelques clients de test
        $clients = Client::factory(5)->create();

        // Pour chaque client, créer des projets
        $clients->each(function (Client $client) {
            $projects = Project::factory(rand(1, 3))->create([
                'client_id' => $client->id,
                'start_date' => Carbon::now()->subMonths(rand(1, 6)),
                'end_date' => Carbon::now()->addMonths(rand(1, 6)),
            ]);

            // Pour chaque projet, créer des événements
            $projects->each(function (Project $project) {
                // Événements step
                Event::factory(rand(2, 5))->create([
                    'project_id' => $project->id,
                    'event_type' => 'step',
                    'status' => ['todo', 'done'][rand(0, 1)],
                    'created_date' => $project->start_date->addDays(rand(1, 30)),
                    'execution_date' => $project->start_date->addDays(rand(30, 90)),
                    'amount' => null,
                    'payment_status' => null,
                    'send_date' => null,
                    'payment_due_date' => null,
                    'paid_at' => null,
                ]);

                // Événements billing
                Event::factory(rand(1, 3))->create([
                    'project_id' => $project->id,
                    'event_type' => 'billing',
                    'status' => ['to_send', 'sent'][rand(0, 1)],
                    'amount' => rand(500, 5000),
                    'payment_status' => ['pending', 'paid'][rand(0, 1)],
                    'created_date' => $project->start_date->addDays(rand(1, 30)),
                    'send_date' => $project->start_date->addDays(rand(30, 60)),
                    'payment_due_date' => $project->start_date->addDays(rand(60, 90)),
                    'execution_date' => null,
                ]);
            });
        });

        $this->command->info('Test data seeded successfully!');
    }
}