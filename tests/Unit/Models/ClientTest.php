<?php

namespace Tests\Unit\Models;

use App\Enums\ProjectStatus;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_client()
    {
        $clientData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '06 12 34 56 78',
            'company' => 'Doe Inc.',
            'address' => '123 Main St',
            'notes' => 'Important client',
        ];

        $client = Client::create($clientData);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertEquals('John Doe', $client->name);
        $this->assertEquals('john@example.com', $client->email);
        $this->assertEquals('06 12 34 56 78', $client->phone);
        $this->assertEquals('Doe Inc.', $client->company);
        $this->assertEquals('123 Main St', $client->address);
        $this->assertEquals('Important client', $client->notes);
        $this->assertDatabaseHas('clients', $clientData);
    }

    /** @test */
    public function it_can_create_a_client_without_optional_fields()
    {
        $clientData = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ];

        $client = Client::create($clientData);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertEquals('Jane Doe', $client->name);
        $this->assertEquals('jane@example.com', $client->email);
        $this->assertNull($client->phone);
        $this->assertNull($client->company);
        $this->assertNull($client->address);
        $this->assertNull($client->notes);
    }

    /** @test */
    public function it_has_projects_relationship()
    {
        $client = Client::factory()->create();
        $project1 = Project::factory()->create(['client_id' => $client->id]);
        $project2 = Project::factory()->create(['client_id' => $client->id]);

        $this->assertCount(2, $client->projects);
        $this->assertTrue($client->projects->contains($project1));
        $this->assertTrue($client->projects->contains($project2));
    }

    /** @test */
    public function it_can_get_active_projects_count()
    {
        $client = Client::factory()->create();
        Project::factory()->count(3)->create([
            'client_id' => $client->id,
            'status' => ProjectStatus::Active->value,
        ]);
        Project::factory()->count(2)->create([
            'client_id' => $client->id,
            'status' => ProjectStatus::Completed->value,
        ]);

        $this->assertEquals(3, $client->active_projects_count);
    }

    /** @test */
    public function it_can_get_completed_projects_count()
    {
        $client = Client::factory()->create();
        Project::factory()->count(2)->create([
            'client_id' => $client->id,
            'status' => ProjectStatus::Completed->value,
        ]);
        Project::factory()->count(3)->create([
            'client_id' => $client->id,
            'status' => ProjectStatus::Active->value,
        ]);

        $this->assertEquals(2, $client->completed_projects_count);
    }

    /** @test */
    public function it_casts_dates_properly()
    {
        $client = Client::factory()->create();

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $client->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $client->updated_at);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $client = new Client();
        $fillable = ['name', 'email', 'phone', 'company', 'address', 'notes'];

        $this->assertEquals($fillable, $client->getFillable());
    }

    /** @test */
    public function it_can_update_client_information()
    {
        $client = Client::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
        ]);

        $client->update([
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);

        $this->assertEquals('Updated Name', $client->fresh()->name);
        $this->assertEquals('updated@example.com', $client->fresh()->email);
    }

    /** @test */
    public function it_can_delete_a_client()
    {
        $client = Client::factory()->create();
        $clientId = $client->id;

        $client->delete();

        $this->assertDatabaseMissing('clients', ['id' => $clientId]);
    }

    /** @test */
    public function it_cascades_when_deleting_client_with_projects()
    {
        $client = Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->id]);

        // With onDelete('cascade') in migration, projects should be deleted
        $client->delete();

        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
        // Projects should be deleted due to cascade
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }
}