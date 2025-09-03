<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Event;
use App\Models\Project;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_step_event_can_be_created()
    {
        $project = $this->createProject();
        $eventData = [
            'project_id' => $project->id,
            'name' => 'Test Step',
            'description' => 'A test step event',
            'type' => 'task',
            'event_type' => 'step',
            'status' => 'todo',
            'created_date' => '2024-01-01',
            'execution_date' => '2024-01-15'
        ];

        $event = Event::factory()->create($eventData);

        $this->assertInstanceOf(Event::class, $event);
        $this->assertEquals($eventData['name'], $event->name);
        $this->assertEquals($eventData['description'], $event->description);
        $this->assertEquals($eventData['type'], $event->type);
        $this->assertEquals($eventData['event_type'], $event->event_type);
        $this->assertEquals($eventData['status'], $event->status);
        $this->assertEquals($eventData['created_date'], $event->created_date->format('Y-m-d'));
        $this->assertEquals($eventData['execution_date'], $event->execution_date->format('Y-m-d'));
    }

    public function test_billing_event_can_be_created()
    {
        $project = $this->createProject();
        $eventData = [
            'project_id' => $project->id,
            'name' => 'Test Invoice',
            'description' => 'A test billing event',
            'type' => 'invoice',
            'event_type' => 'billing',
            'status' => 'to_send',
            'created_date' => '2024-01-01',
            'send_date' => '2024-01-15',
            'payment_due_date' => '2024-02-15',
            'amount' => 1500.00,
            'payment_status' => 'pending'
        ];

        $event = Event::factory()->create($eventData);

        $this->assertInstanceOf(Event::class, $event);
        $this->assertEquals($eventData['name'], $event->name);
        $this->assertEquals($eventData['event_type'], $event->event_type);
        $this->assertEquals($eventData['status'], $event->status);
        $this->assertEquals($eventData['amount'], $event->amount);
        $this->assertEquals($eventData['payment_status'], $event->payment_status);
        $this->assertEquals($eventData['send_date'], $event->send_date->format('Y-m-d'));
        $this->assertEquals($eventData['payment_due_date'], $event->payment_due_date->format('Y-m-d'));
    }

    public function test_event_belongs_to_project()
    {
        $project = $this->createProject();
        $event = $this->createEvent(['project_id' => $project->id]);

        $this->assertInstanceOf(Project::class, $event->project);
        $this->assertEquals($project->id, $event->project->id);
        $this->assertEquals($project->name, $event->project->name);
    }

    public function test_event_has_client_through_project()
    {
        $client = $this->createClient();
        $project = $this->createProject(['client_id' => $client->id]);
        $event = $this->createEvent(['project_id' => $project->id]);

        // Assuming there's a client relationship through project
        $this->assertEquals($client->id, $event->project->client->id);
        $this->assertEquals($client->name, $event->project->client->name);
    }

    public function test_step_event_status_enum_values()
    {
        $project = $this->createProject();
        $validStatuses = ['todo', 'done', 'cancelled'];

        foreach ($validStatuses as $status) {
            $event = $this->createEvent([
                'project_id' => $project->id,
                'event_type' => 'step',
                'status' => $status
            ]);
            $this->assertEquals($status, $event->status);
        }
    }

    public function test_billing_event_status_enum_values()
    {
        $project = $this->createProject();
        $validStatuses = ['to_send', 'sent', 'cancelled'];

        foreach ($validStatuses as $status) {
            $event = $this->createEvent([
                'project_id' => $project->id,
                'event_type' => 'billing',
                'status' => $status
            ]);
            $this->assertEquals($status, $event->status);
        }
    }

    public function test_billing_event_payment_status_enum_values()
    {
        $project = $this->createProject();
        $validPaymentStatuses = ['pending', 'paid'];

        foreach ($validPaymentStatuses as $paymentStatus) {
            $event = $this->createEvent([
                'project_id' => $project->id,
                'event_type' => 'billing',
                'payment_status' => $paymentStatus
            ]);
            $this->assertEquals($paymentStatus, $event->payment_status);
        }
    }

    public function test_event_type_enum_values()
    {
        $project = $this->createProject();
        $validEventTypes = ['step', 'billing'];

        foreach ($validEventTypes as $eventType) {
            $event = $this->createEvent([
                'project_id' => $project->id,
                'event_type' => $eventType
            ]);
            $this->assertEquals($eventType, $event->event_type);
        }
    }

    public function test_event_name_is_required()
    {
        $project = $this->createProject();
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        Event::factory()->create([
            'project_id' => $project->id,
            'name' => null
        ]);
    }

    public function test_event_project_id_is_required()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        Event::factory()->create(['project_id' => null]);
    }

    public function test_event_event_type_is_required()
    {
        $project = $this->createProject();
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        Event::factory()->create([
            'project_id' => $project->id,
            'event_type' => null
        ]);
    }

    public function test_step_event_does_not_require_billing_fields()
    {
        $project = $this->createProject();
        $event = $this->createEvent([
            'project_id' => $project->id,
            'event_type' => 'step',
            'amount' => null,
            'payment_status' => null,
            'send_date' => null,
            'payment_due_date' => null,
            'paid_at' => null
        ]);

        $this->assertNull($event->amount);
        $this->assertNull($event->payment_status);
        $this->assertNull($event->send_date);
        $this->assertNull($event->payment_due_date);
        $this->assertNull($event->paid_at);
    }

    public function test_billing_event_does_not_require_step_fields()
    {
        $project = $this->createProject();
        $event = $this->createEvent([
            'project_id' => $project->id,
            'event_type' => 'billing',
            'execution_date' => null
        ]);

        $this->assertNull($event->execution_date);
    }

    public function test_event_has_fillable_attributes()
    {
        $fillable = [
            'project_id', 'name', 'description', 'type', 'event_type', 
            'status', 'amount', 'payment_status', 'created_date', 'execution_date', 'send_date', 
            'payment_due_date', 'completed_at', 'paid_at'
        ];
        $event = new Event();
        
        $this->assertEquals($fillable, $event->getFillable());
    }

    public function test_event_casts_dates_properly()
    {
        $event = $this->createEvent([
            'created_date' => '2024-01-01',
            'execution_date' => '2024-01-15',
            'send_date' => '2024-01-10',
            'payment_due_date' => '2024-02-10',
            'paid_at' => '2024-01-20 14:30:00'
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $event->created_date);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $event->execution_date);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $event->send_date);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $event->payment_due_date);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $event->paid_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $event->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $event->updated_at);
    }

    public function test_event_amount_is_cast_to_decimal_string()
    {
        $event = $this->createEvent([
            'event_type' => 'billing',
            'amount' => 1234.56
        ]);
        
        // Laravel decimal:2 cast returns a string, not a float
        $this->assertIsString($event->amount);
        $this->assertEquals('1234.56', $event->amount);
    }

    public function test_event_has_correct_table_name()
    {
        $event = new Event();
        $this->assertEquals('events', $event->getTable());
    }

    public function test_event_project_relationship_returns_correct_type()
    {
        $event = $this->createEvent();
        
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            $event->project()
        );
    }

    public function test_deleting_event_does_not_delete_project()
    {
        $project = $this->createProject();
        $event = $this->createEvent(['project_id' => $project->id]);
        
        $event->delete();
        
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
        $this->assertDatabaseHas('projects', ['id' => $project->id]);
    }

    public function test_step_event_can_have_execution_date()
    {
        $event = $this->createEvent([
            'event_type' => 'step',
            'execution_date' => '2024-01-15'
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $event->execution_date);
        $this->assertEquals('2024-01-15', $event->execution_date->format('Y-m-d'));
    }

    public function test_billing_event_can_be_marked_as_paid()
    {
        $event = $this->createEvent([
            'event_type' => 'billing',
            'payment_status' => 'paid',
            'paid_at' => now()
        ]);

        $this->assertEquals('paid', $event->payment_status);
        $this->assertNotNull($event->paid_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $event->paid_at);
    }

    public function test_event_can_have_different_types_per_event_type()
    {
        $stepEvent = $this->createEvent([
            'event_type' => 'step',
            'type' => 'task'
        ]);

        $billingEvent = $this->createEvent([
            'event_type' => 'billing',
            'type' => 'invoice'
        ]);

        $this->assertEquals('task', $stepEvent->type);
        $this->assertEquals('invoice', $billingEvent->type);
    }
}