<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseConfigTest extends TestCase
{
    public function test_uses_test_database(): void
    {
        // Vérifier que nous utilisons bien la base de données de test
        $database = DB::connection()->getDatabaseName();
        
        $this->assertEquals('client-manager-test', $database, 
            'Tests should use the test database, not the main application database'
        );
    }
    
    public function test_environment_is_testing(): void
    {
        $this->assertEquals('testing', app()->environment());
    }
}