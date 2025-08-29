<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Event;
use App\Models\Project;
use App\Models\User;
use App\Enums\ProjectStatus;
use App\Enums\EventType;
use App\Enums\EventStatus;
use App\Enums\PaymentStatus;
use App\Enums\EventCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimpleDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();
        
        try {
            // Create users
            User::updateOrCreate(['email' => 'admin@example.com'], [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]);
            
            User::updateOrCreate(['email' => 'test@example.com'], [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]);
            
            // Create simple test data
            $client1 = Client::create([
                'name' => 'Jean Dupont',
                'email' => 'jean.dupont@example.com',
                'phone' => '06 12 34 56 78',
                'company' => 'Dupont SARL',
                'address' => '123 rue de la République\n75001 Paris',
                'notes' => 'Client fidèle depuis 2020'
            ]);
            
            $client2 = Client::create([
                'name' => 'Marie Martin',
                'email' => 'marie.martin@example.com',
                'phone' => '07 98 76 54 32',
                'company' => 'Martin Consulting',
                'address' => '45 avenue des Champs\n69000 Lyon',
                'notes' => null
            ]);
            
            // Create projects
            $project1 = Project::create([
                'client_id' => $client1->id,
                'name' => 'Refonte site web',
                'description' => "Modernisation complète du site web avec nouvelle charte graphique.\n\nLivrables:\n- Maquettes\n- Développement\n- Tests",
                'status' => ProjectStatus::Active->value,
                'start_date' => now()->subMonths(2),
                'end_date' => now()->addMonths(1),
                'budget' => 15000
            ]);
            
            $project2 = Project::create([
                'client_id' => $client2->id,
                'name' => 'Application mobile',
                'description' => "Développement d'une application mobile iOS et Android",
                'status' => ProjectStatus::Active->value,
                'start_date' => now()->subMonth(),
                'end_date' => now()->addMonths(3),
                'budget' => 35000
            ]);
            
            // Create events for project 1
            Event::create([
                'project_id' => $project1->id,
                'name' => 'Réunion de lancement',
                'description' => 'Définition des objectifs et planning',
                'type' => EventCategory::Meeting->value,
                'event_type' => EventType::Step->value,
                'status' => EventStatus::Done->value,
                'created_date' => now()->subMonths(2),
                'execution_date' => now()->subMonths(2)->addDays(1),
                'completed_at' => now()->subMonths(2)->addDays(1),
            ]);
            
            Event::create([
                'project_id' => $project1->id,
                'name' => 'Développement phase 1',
                'description' => 'Développement des fonctionnalités principales',
                'type' => EventCategory::Development->value,
                'event_type' => EventType::Step->value,
                'status' => EventStatus::Todo->value,
                'created_date' => now()->subMonth(),
                'execution_date' => now()->addWeek(),
            ]);
            
            Event::create([
                'project_id' => $project1->id,
                'name' => 'Facture acompte 30%',
                'description' => 'Acompte sur le projet',
                'type' => EventCategory::Invoice->value,
                'event_type' => EventType::Billing->value,
                'status' => EventStatus::Sent->value,
                'amount' => 4500,
                'payment_status' => PaymentStatus::Paid->value,
                'created_date' => now()->subMonths(2),
                'send_date' => now()->subMonths(2)->addDays(2),
                'payment_due_date' => now()->subMonth(),
                'paid_at' => now()->subMonth()->addDays(5),
            ]);
            
            // Create events for project 2
            Event::create([
                'project_id' => $project2->id,
                'name' => 'Conception UX/UI',
                'description' => 'Création des maquettes',
                'type' => EventCategory::Design->value,
                'event_type' => EventType::Step->value,
                'status' => EventStatus::Todo->value,
                'created_date' => now()->subWeek(),
                'execution_date' => now()->addDays(3),
            ]);
            
            Event::create([
                'project_id' => $project2->id,
                'name' => 'Devis initial',
                'description' => 'Proposition commerciale',
                'type' => EventCategory::Quote->value,
                'event_type' => EventType::Billing->value,
                'status' => EventStatus::Sent->value,
                'amount' => 35000,
                'payment_status' => null,
                'created_date' => now()->subMonth(),
                'send_date' => now()->subMonth()->addDay(),
            ]);
            
            // Create more clients with projects
            for ($i = 3; $i <= 10; $i++) {
                $client = Client::create([
                    'name' => "Client Test $i",
                    'email' => "client$i@example.com",
                    'phone' => "06 " . rand(10, 99) . " " . rand(10, 99) . " " . rand(10, 99) . " " . rand(10, 99),
                    'company' => rand(0, 1) ? "Entreprise $i" : null,
                    'address' => "$i rue Example\n75000 Paris",
                ]);
                
                // Create 1-3 projects per client
                $projectCount = rand(1, 3);
                for ($j = 1; $j <= $projectCount; $j++) {
                    $status = collect([ProjectStatus::Active, ProjectStatus::Completed, ProjectStatus::OnHold])->random();
                    $project = Project::create([
                        'client_id' => $client->id,
                        'name' => "Projet $j pour Client $i",
                        'description' => "Description du projet $j",
                        'status' => $status->value,
                        'start_date' => now()->subMonths(rand(1, 6)),
                        'end_date' => now()->addMonths(rand(1, 6)),
                        'budget' => rand(5, 50) * 1000,
                    ]);
                    
                    // Create 2-5 events per project
                    $eventCount = rand(2, 5);
                    for ($k = 1; $k <= $eventCount; $k++) {
                        $eventType = rand(0, 1) ? EventType::Step : EventType::Billing;
                        
                        if ($eventType === EventType::Step) {
                            Event::create([
                                'project_id' => $project->id,
                                'name' => "Tâche $k du projet $j",
                                'type' => collect(EventCategory::forEventType(EventType::Step))->random()->value,
                                'event_type' => EventType::Step->value,
                                'status' => collect([EventStatus::Todo, EventStatus::Done])->random()->value,
                                'created_date' => now()->subMonths(rand(0, 3)),
                                'execution_date' => now()->addDays(rand(-30, 30)),
                            ]);
                        } else {
                            Event::create([
                                'project_id' => $project->id,
                                'name' => "Facture $k du projet $j",
                                'type' => collect(EventCategory::forEventType(EventType::Billing))->random()->value,
                                'event_type' => EventType::Billing->value,
                                'status' => collect([EventStatus::ToSend, EventStatus::Sent])->random()->value,
                                'amount' => rand(10, 100) * 100,
                                'payment_status' => rand(0, 1) ? PaymentStatus::Paid->value : PaymentStatus::Pending->value,
                                'created_date' => now()->subMonths(rand(0, 3)),
                                'send_date' => now()->addDays(rand(-30, 30)),
                                'payment_due_date' => now()->addDays(rand(0, 60)),
                            ]);
                        }
                    }
                }
            }
            
            DB::commit();
            
            echo "✅ Seeding completed successfully!\n";
            echo "Created:\n";
            echo "- " . User::count() . " users\n";
            echo "- " . Client::count() . " clients\n";
            echo "- " . Project::count() . " projects\n";
            echo "- " . Event::count() . " events\n";
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}