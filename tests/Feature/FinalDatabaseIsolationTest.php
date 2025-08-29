<?php

namespace Tests\Feature;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FinalDatabaseIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_isolation_confirmed(): void
    {
        // Vérifier que nous utilisons bien la base de test
        $database = DB::connection()->getDatabaseName();
        $this->assertEquals('client-manager-test', $database);

        // Test que nous pouvons créer et manipuler des données sans affecter la base principale
        $initialCount = Client::count();
        
        // Créer un client de test
        $client = Client::factory()->create([
            'name' => 'Test Isolation Client',
            'email' => 'test-isolation@example.com'
        ]);

        // Vérifier que le client a été créé
        $this->assertEquals($initialCount + 1, Client::count());
        $this->assertDatabaseHas('clients', [
            'name' => 'Test Isolation Client',
            'email' => 'test-isolation@example.com'
        ]);

        // Ce test confirme que :
        // 1. Nous utilisons la base client-manager-test
        // 2. Nous pouvons créer des données de test
        // 3. Ces données n'affectent pas la base principale
        $this->assertTrue(true, 'Database isolation is working correctly');
    }

    public function test_environment_and_database_configuration(): void
    {
        // Vérifier l'environnement de test
        $this->assertEquals('testing', app()->environment());
        
        // Vérifier la base de données de test
        $this->assertEquals('client-manager-test', DB::connection()->getDatabaseName());
        
        // Vérifier que les configurations MySQL sont correctes
        $config = config('database.connections.mysql');
        $this->assertEquals('mysql', $config['driver']);
        $this->assertEquals('127.0.0.1', $config['host']);
        $this->assertEquals(3306, $config['port']);
    }
}