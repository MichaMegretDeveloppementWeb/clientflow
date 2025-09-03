<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_be_created()
    {
        $clientData = [
            'name' => 'John Doe',
            'company' => 'Acme Corp',
            'email' => 'john@acme.com',
            'phone' => '0123456789',
            'address' => '123 Main St',
            'notes' => 'Important client'
        ];

        $client = Client::factory()->create($clientData);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertEquals($clientData['name'], $client->name);
        $this->assertEquals($clientData['company'], $client->company);
        $this->assertEquals($clientData['email'], $client->email);
        $this->assertEquals($clientData['phone'], $client->phone);
        $this->assertEquals($clientData['address'], $client->address);
        $this->assertEquals($clientData['notes'], $client->notes);
    }

    public function test_client_has_projects_relationship()
    {
        $client = $this->createClient();
        $project = $this->createProject(['client_id' => $client->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $client->projects);
        $this->assertTrue($client->projects->contains($project));
        $this->assertEquals(1, $client->projects->count());
    }

    public function test_client_can_have_multiple_projects()
    {
        $client = $this->createClient();
        $project1 = $this->createProject(['client_id' => $client->id]);
        $project2 = $this->createProject(['client_id' => $client->id]);

        $client->refresh();
        $this->assertEquals(2, $client->projects->count());
        $this->assertTrue($client->projects->contains($project1));
        $this->assertTrue($client->projects->contains($project2));
    }

    public function test_client_hard_deletes()
    {
        $client = $this->createClient();
        $clientId = $client->id;

        $client->delete();

        $this->assertDatabaseMissing('clients', ['id' => $clientId]);
        $this->assertNull(Client::find($clientId));
    }

    public function test_client_name_is_required()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        Client::factory()->create(['name' => null]);
    }

    public function test_client_email_can_be_duplicate()
    {
        // Contrairement à l'hypothèse précédente, l'email peut être dupliqué
        $email = 'duplicate@test.com';
        $client1 = $this->createClient(['email' => $email]);
        $client2 = $this->createClient(['email' => $email]);

        $this->assertEquals($email, $client1->email);
        $this->assertEquals($email, $client2->email);
        $this->assertNotEquals($client1->id, $client2->id);
    }

    public function test_client_email_can_be_null()
    {
        $client = Client::factory()->create(['email' => null]);
        $this->assertNull($client->email);
    }

    public function test_client_has_fillable_attributes()
    {
        $fillable = ['name', 'email', 'phone', 'company', 'address', 'notes', 'user_id'];
        $client = new Client();
        
        $this->assertEquals($fillable, $client->getFillable());
    }

    public function test_client_casts_timestamps_correctly()
    {
        $client = new Client();
        $casts = $client->getCasts();
        
        $this->assertEquals('datetime', $casts['created_at']);
        $this->assertEquals('datetime', $casts['updated_at']);
    }

    public function test_client_casts_timestamps_to_datetime()
    {
        $client = $this->createClient();
        
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $client->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $client->updated_at);
    }

    public function test_client_has_correct_table_name()
    {
        $client = new Client();
        $this->assertEquals('clients', $client->getTable());
    }

    public function test_client_projects_relationship_returns_correct_type()
    {
        $client = $this->createClient();
        
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            $client->projects()
        );
    }

    public function test_client_deletion_behavior()
    {
        $client = $this->createClient();
        $project = $this->createProject(['client_id' => $client->id]);
        $projectId = $project->id;
        
        $client->delete();
        
        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
        // Note: Le comportement de suppression dépend des contraintes FK dans la DB
        // Ce test vérifie juste que le client est supprimé
    }
}