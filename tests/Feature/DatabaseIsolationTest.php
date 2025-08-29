<?php

namespace Tests\Feature;

use App\Models\Client;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DatabaseIsolationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_test_database_isolation(): void
    {
        // Dans les tests, nous devons avoir une base de données vide au départ
        $this->assertEquals(0, Client::count(), 'Test database should start empty');

        // Créer un client de test
        $client = Client::factory()->create([
            'name' => 'Test Client for Isolation',
            'email' => 'isolation-test@example.com'
        ]);

        // Vérifier que le client existe dans la base de test
        $this->assertEquals(1, Client::count());
        $this->assertEquals('Test Client for Isolation', $client->name);

        // Ce test confirme que nous travaillons avec une base de données de test isolée
        $this->assertDatabaseHas('clients', [
            'name' => 'Test Client for Isolation',
            'email' => 'isolation-test@example.com'
        ]);
    }

    public function test_test_data_does_not_persist_between_tests(): void
    {
        // Ce test vérifie que RefreshDatabase fonctionne correctement
        $this->assertEquals(0, Client::count(), 'Database should be refreshed between tests');

        // Créer des données
        Client::factory()->create(['name' => 'Temporary Client']);
        $this->assertEquals(1, Client::count());

        // Ces données ne devraient pas persister au test suivant
    }

    public function test_confirm_test_data_isolation(): void
    {
        // Ce test confirme que les données du test précédent ont été supprimées
        $this->assertEquals(0, Client::count(), 'Test data should not persist between tests');
    }
}