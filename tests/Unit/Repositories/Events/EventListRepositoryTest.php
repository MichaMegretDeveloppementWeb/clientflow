<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories\Events;

use App\Models\Client;
use App\Models\Event;
use App\Models\Project;
use App\Models\User;
use App\Repositories\Events\EventListRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class EventListRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EventListRepository $repository;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->repository = new EventListRepository();
        $this->user = $this->actingAsUser();
    }

    public function test_get_paginated_filters_by_authenticated_user(): void
    {
        // Créer un autre utilisateur avec ses données
        $otherUser = User::factory()->create();
        $otherClient = Client::factory()->for($otherUser)->create();
        $otherProject = Project::factory()->for($otherClient)->create();
        Event::factory()->for($otherProject)->create(['name' => 'Other User Event']);

        // Créer des données pour l'utilisateur connecté
        $client = $this->createClient();
        $project = $this->createProject(['client_id' => $client->id]);
        Event::factory()->for($project)->create(['name' => 'My Event']);

        $result = $this->repository->paginate(15);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(1, $result->total());
        $this->assertEquals('My Event', $result->items()[0]->name);
    }

    public function test_get_paginated_returns_correct_structure(): void
    {
        $client = $this->createClient();
        $project = $this->createProject(['client_id' => $client->id]);
        Event::factory()->for($project)->create();

        $result = $this->repository->paginate(15);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $event = $result->items()[0];
        
        // Vérifier que les relations sont chargées
        $this->assertTrue($event->relationLoaded('project'));
        $this->assertTrue($event->project->relationLoaded('client'));
    }

    public function test_get_paginated_filters_by_search(): void
    {
        $client = $this->createClient();
        $project = $this->createProject(['client_id' => $client->id]);
        
        Event::factory()->for($project)->create(['name' => 'Meeting with client']);
        Event::factory()->for($project)->create(['name' => 'Code review']);
        Event::factory()->for($project)->create(['description' => 'Important meeting details']);

        // Test recherche dans le nom
        $result = $this->repository->paginate(15, ['search' => 'meeting']);
        $this->assertEquals(2, $result->total());

        // Test recherche dans la description
        $result = $this->repository->paginate(15, ['search' => 'important']);
        $this->assertEquals(1, $result->total());

        // Test recherche case insensitive
        $result = $this->repository->paginate(15, ['search' => 'MEETING']);
        $this->assertEquals(2, $result->total());
    }

    public function test_get_paginated_filters_by_project_id(): void
    {
        $client = $this->createClient();
        $project1 = $this->createProject(['client_id' => $client->id, 'name' => 'Project 1']);
        $project2 = $this->createProject(['client_id' => $client->id, 'name' => 'Project 2']);
        
        Event::factory()->for($project1)->count(2)->create();
        Event::factory()->for($project2)->count(3)->create();

        $result = $this->repository->paginate(15, ['project_id' => $project1->id]);
        $this->assertEquals(2, $result->total());

        $result = $this->repository->paginate(15, ['project_id' => $project2->id]);
        $this->assertEquals(3, $result->total());
    }

    public function test_get_paginated_filters_by_client_id(): void
    {
        $client1 = $this->createClient(['name' => 'Client 1']);
        $client2 = $this->createClient(['name' => 'Client 2']);
        
        $project1 = $this->createProject(['client_id' => $client1->id]);
        $project2 = $this->createProject(['client_id' => $client2->id]);
        
        Event::factory()->for($project1)->count(2)->create();
        Event::factory()->for($project2)->count(3)->create();

        $result = $this->repository->paginate(15, ['client_id' => $client1->id]);
        $this->assertEquals(2, $result->total());

        $result = $this->repository->paginate(15, ['client_id' => $client2->id]);
        $this->assertEquals(3, $result->total());
    }

    public function test_get_paginated_filters_by_event_type(): void
    {
        $client = $this->createClient();
        $project = $this->createProject(['client_id' => $client->id]);
        
        Event::factory()->for($project)->create(['event_type' => 'step']);
        Event::factory()->for($project)->create(['event_type' => 'step']);
        Event::factory()->for($project)->create(['event_type' => 'billing']);

        $result = $this->repository->paginate(15, ['event_type' => 'step']);
        $this->assertEquals(2, $result->total());

        $result = $this->repository->paginate(15, ['event_type' => 'billing']);
        $this->assertEquals(1, $result->total());
    }

    public function test_get_paginated_filters_by_type(): void
    {
        $client = $this->createClient();
        $project = $this->createProject(['client_id' => $client->id]);
        
        Event::factory()->for($project)->create(['type' => 'meeting']);
        Event::factory()->for($project)->create(['type' => 'task']);
        Event::factory()->for($project)->create(['type' => 'invoice']);

        $result = $this->repository->paginate(15, ['type' => 'meeting']);
        $this->assertEquals(1, $result->total());

        $result = $this->repository->paginate(15, ['type' => 'task']);
        $this->assertEquals(1, $result->total());
    }

    public function test_get_paginated_filters_by_status(): void
    {
        $client = $this->createClient();
        $project = $this->createProject(['client_id' => $client->id]);
        
        Event::factory()->for($project)->create(['status' => 'todo', 'event_type' => 'step']);
        Event::factory()->for($project)->create(['status' => 'done', 'event_type' => 'step']);
        Event::factory()->for($project)->create(['status' => 'to_send', 'event_type' => 'billing']);

        $result = $this->repository->paginate(15, ['status' => 'todo']);
        $this->assertEquals(2, $result->total()); // todo inclut 'todo' + 'to_send'

        $result = $this->repository->paginate(15, ['status' => 'done']);
        $this->assertEquals(1, $result->total());
    }

    public function test_get_paginated_sorts_by_created_date(): void
    {
        $client = $this->createClient();
        $project = $this->createProject(['client_id' => $client->id]);
        
        $event1 = Event::factory()->for($project)->create(['name' => 'First', 'created_date' => now()->subDays(2)]);
        $event2 = Event::factory()->for($project)->create(['name' => 'Second', 'created_date' => now()->subDays(1)]);
        $event3 = Event::factory()->for($project)->create(['name' => 'Third', 'created_date' => now()]);

        // Test tri descendant (défaut)
        $result = $this->repository->paginate(15);
        $items = $result->items();
        $this->assertEquals('Third', $items[0]->name);
        $this->assertEquals('Second', $items[1]->name);
        $this->assertEquals('First', $items[2]->name);

        // Test tri ascendant
        $result = $this->repository->paginate(15, ['sort' => 'created_date', 'direction' => 'asc']);
        $items = $result->items();
        $this->assertEquals('First', $items[0]->name);
        $this->assertEquals('Second', $items[1]->name);
        $this->assertEquals('Third', $items[2]->name);
    }

    public function test_get_paginated_sorts_by_name(): void
    {
        $client = $this->createClient();
        $project = $this->createProject(['client_id' => $client->id]);
        
        Event::factory()->for($project)->create(['name' => 'Charlie Event']);
        Event::factory()->for($project)->create(['name' => 'Alpha Event']);
        Event::factory()->for($project)->create(['name' => 'Beta Event']);

        $result = $this->repository->paginate(15, ['sort' => 'name', 'direction' => 'asc']);
        $items = $result->items();
        
        $this->assertEquals('Alpha Event', $items[0]->name);
        $this->assertEquals('Beta Event', $items[1]->name);
        $this->assertEquals('Charlie Event', $items[2]->name);
    }

    public function test_get_paginated_handles_pagination(): void
    {
        $client = $this->createClient();
        $project = $this->createProject(['client_id' => $client->id]);
        
        Event::factory()->for($project)->count(25)->create();

        $result = $this->repository->paginate(10);
        
        $this->assertEquals(25, $result->total());
        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(3, $result->lastPage());
        $this->assertEquals(10, count($result->items()));
    }

    public function test_get_global_statistics_returns_correct_structure(): void
    {
        $client = $this->createClient();
        $project = $this->createProject(['client_id' => $client->id]);
        
        // Créer différents types d'événements
        Event::factory()->for($project)->create(['event_type' => 'step', 'status' => 'todo']);
        Event::factory()->for($project)->create(['event_type' => 'step', 'status' => 'done']);
        Event::factory()->for($project)->create(['event_type' => 'billing', 'status' => 'to_send', 'amount' => 1000]);
        Event::factory()->for($project)->create(['event_type' => 'billing', 'status' => 'sent', 'amount' => 2000]);

        $stats = $this->repository->getGlobalStatistics();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total', $stats);
        $this->assertArrayHasKey('todo', $stats);
        $this->assertArrayHasKey('done', $stats);
        $this->assertArrayHasKey('overdue', $stats);
        $this->assertEquals(4, $stats['total']);
        $this->assertEquals(2, $stats['todo']); // todo + to_send
        $this->assertEquals(2, $stats['done']); // done + sent
    }

    public function test_get_global_statistics_filters_by_user(): void
    {
        // Créer des données pour un autre utilisateur
        $otherUser = User::factory()->create();
        $otherClient = Client::factory()->for($otherUser)->create();
        $otherProject = Project::factory()->for($otherClient)->create();
        Event::factory()->for($otherProject)->count(5)->create(['event_type' => 'billing', 'amount' => 8250]);

        // Créer des données pour l'utilisateur connecté
        $client = $this->createClient();
        $project = $this->createProject(['client_id' => $client->id]);
        
        // Créer manuellement 3 événements billing simples
        for ($i = 0; $i < 3; $i++) {
            Event::create([
                'project_id' => $project->id,
                'name' => 'Test Billing Event ' . ($i + 1),
                'description' => 'Test description',
                'type' => 'invoice',
                'event_type' => 'billing',
                'status' => 'to_send',
                'amount' => 3150,
                'created_date' => now()->subDays($i + 1),
                'send_date' => now()->subDays($i),
            ]);
        }

        $stats = $this->repository->getGlobalStatistics();

        $this->assertEquals(3, $stats['total']);
        $this->assertEquals(3, $stats['todo']); // 3 billing events with status 'to_send'
    }

    public function test_get_global_statistics_handles_empty_data(): void
    {
        $stats = $this->repository->getGlobalStatistics();

        $this->assertIsArray($stats);
        $this->assertEquals(0, $stats['total']);
        $this->assertEquals(0, $stats['todo']);
        $this->assertEquals(0, $stats['done']);
        $this->assertEquals(0, $stats['overdue']);
    }

    public function test_get_paginated_combines_multiple_filters(): void
    {
        $client1 = $this->createClient(['name' => 'Client 1']);
        $client2 = $this->createClient(['name' => 'Client 2']);
        
        $project1 = $this->createProject(['client_id' => $client1->id]);
        $project2 = $this->createProject(['client_id' => $client2->id]);
        
        // Events pour client 1
        Event::factory()->for($project1)->create([
            'name' => 'Meeting task',
            'event_type' => 'step',
            'status' => 'todo'
        ]);
        Event::factory()->for($project1)->create([
            'name' => 'Review task',
            'event_type' => 'step',
            'status' => 'done'
        ]);
        
        // Events pour client 2
        Event::factory()->for($project2)->create([
            'name' => 'Meeting invoice',
            'event_type' => 'billing',
            'status' => 'to_send'
        ]);

        // Test combinaison client_id + search + event_type
        $result = $this->repository->paginate(15, [
            'client_id' => $client1->id,
            'search' => 'task',
            'event_type' => 'step'
        ]);
        
        $this->assertEquals(2, $result->total());
    }
}