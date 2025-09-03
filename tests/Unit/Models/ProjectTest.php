<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Project;
use App\Models\Client;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_can_be_created()
    {
        $client = $this->createClient();
        $projectData = [
            'client_id' => $client->id,
            'name' => 'Test Project',
            'description' => 'A test project description',
            'status' => 'active',
            'budget' => 5000.00,
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31'
        ];

        $project = Project::factory()->create($projectData);

        $this->assertInstanceOf(Project::class, $project);
        $this->assertEquals($projectData['name'], $project->name);
        $this->assertEquals($projectData['description'], $project->description);
        $this->assertEquals($projectData['status'], $project->status);
        $this->assertEquals($projectData['budget'], $project->budget);
        $this->assertEquals($projectData['start_date'], $project->start_date->format('Y-m-d'));
        $this->assertEquals($projectData['end_date'], $project->end_date->format('Y-m-d'));
    }

    public function test_project_belongs_to_client()
    {
        $client = $this->createClient();
        $project = $this->createProject(['client_id' => $client->id]);

        $this->assertInstanceOf(Client::class, $project->client);
        $this->assertEquals($client->id, $project->client->id);
        $this->assertEquals($client->name, $project->client->name);
    }

    public function test_project_has_events_relationship()
    {
        $project = $this->createProject();
        $stepEvent = $this->createEvent([
            'project_id' => $project->id,
            'event_type' => 'step'
        ]);
        $billingEvent = $this->createEvent([
            'project_id' => $project->id,
            'event_type' => 'billing'
        ]);

        $project->refresh();
        $this->assertEquals(2, $project->events->count());
        $this->assertTrue($project->events->contains($stepEvent));
        $this->assertTrue($project->events->contains($billingEvent));
    }

    public function test_project_can_filter_step_events()
    {
        $project = $this->createProject();
        $stepEvent = $this->createEvent([
            'project_id' => $project->id,
            'event_type' => 'step'
        ]);
        $billingEvent = $this->createEvent([
            'project_id' => $project->id,
            'event_type' => 'billing'
        ]);

        $stepEvents = $project->events()->where('event_type', 'step')->get();
        $this->assertEquals(1, $stepEvents->count());
        $this->assertTrue($stepEvents->contains($stepEvent));
        $this->assertFalse($stepEvents->contains($billingEvent));
    }

    public function test_project_can_filter_billing_events()
    {
        $project = $this->createProject();
        $stepEvent = $this->createEvent([
            'project_id' => $project->id,
            'event_type' => 'step'
        ]);
        $billingEvent = $this->createEvent([
            'project_id' => $project->id,
            'event_type' => 'billing'
        ]);

        $billingEvents = $project->events()->where('event_type', 'billing')->get();
        $this->assertEquals(1, $billingEvents->count());
        $this->assertTrue($billingEvents->contains($billingEvent));
        $this->assertFalse($billingEvents->contains($stepEvent));
    }

    public function test_project_status_enum_values()
    {
        $validStatuses = ['active', 'completed', 'on_hold', 'cancelled'];

        foreach ($validStatuses as $status) {
            $project = $this->createProject(['status' => $status]);
            $this->assertEquals($status, $project->status);
        }
    }

    public function test_project_name_is_required()
    {
        $client = $this->createClient();
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        Project::factory()->create([
            'client_id' => $client->id,
            'name' => null
        ]);
    }

    public function test_project_client_id_is_required()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        Project::factory()->create(['client_id' => null]);
    }

    public function test_project_has_fillable_attributes()
    {
        $fillable = [
            'client_id', 'name', 'description', 'status', 
            'start_date', 'end_date', 'budget'
        ];
        $project = new Project();
        
        $this->assertEquals($fillable, $project->getFillable());
    }

    public function test_project_casts_dates_properly()
    {
        $project = $this->createProject([
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31'
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $project->start_date);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $project->end_date);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $project->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $project->updated_at);
    }

    public function test_project_budget_is_cast_to_decimal_string()
    {
        $project = $this->createProject(['budget' => 1234.56]);
        
        // Laravel decimal:2 cast returns a string, not a float
        $this->assertIsString($project->budget);
        $this->assertEquals('1234.56', $project->budget);
    }

    public function test_project_can_have_null_description()
    {
        $project = $this->createProject(['description' => null]);
        $this->assertNull($project->description);
    }

    public function test_project_can_have_null_budget()
    {
        $project = $this->createProject(['budget' => null]);
        $this->assertNull($project->budget);
    }

    public function test_project_can_have_null_dates()
    {
        $project = $this->createProject([
            'start_date' => null,
            'end_date' => null
        ]);
        
        $this->assertNull($project->start_date);
        $this->assertNull($project->end_date);
    }

    public function test_project_has_correct_table_name()
    {
        $project = new Project();
        $this->assertEquals('projects', $project->getTable());
    }

    public function test_project_relationships_return_correct_types()
    {
        $project = $this->createProject();
        
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            $project->client()
        );
        
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            $project->events()
        );
        
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\HasOne::class,
            $project->latest_event()
        );
    }

    public function test_deleting_project_does_not_delete_client()
    {
        $client = $this->createClient();
        $project = $this->createProject(['client_id' => $client->id]);
        
        $project->delete();
        
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
        $this->assertDatabaseHas('clients', ['id' => $client->id]);
    }

    public function test_project_deletion_behavior()
    {
        $project = $this->createProject();
        $event = $this->createEvent(['project_id' => $project->id]);
        $projectId = $project->id;
        
        $project->delete();
        
        $this->assertDatabaseMissing('projects', ['id' => $projectId]);
        // Note: Le comportement de suppression dépend des contraintes FK dans la DB
    }
}