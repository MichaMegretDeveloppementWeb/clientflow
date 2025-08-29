<?php

namespace Tests\Unit\Models;

use App\Enums\EventCategory;
use App\Enums\EventStatus;
use App\Enums\EventType;
use App\Enums\PaymentStatus;
use App\Models\Event;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_step_event()
    {
        $project = Project::factory()->create();
        $eventData = [
            'project_id' => $project->id,
            'name' => 'Réunion de lancement',
            'description' => 'Première réunion avec le client',
            'type' => EventCategory::Meeting->value,
            'event_type' => EventType::Step->value,
            'status' => EventStatus::Todo->value,
            'created_date' => now()->subDays(5),
            'execution_date' => now()->addDays(2),
        ];

        $event = Event::create($eventData);

        $this->assertInstanceOf(Event::class, $event);
        $this->assertEquals('Réunion de lancement', $event->name);
        $this->assertEquals(EventType::Step->value, $event->event_type);
        $this->assertEquals(EventStatus::Todo->value, $event->status);
        $this->assertNull($event->amount);
        $this->assertNull($event->payment_status);
    }

    /** @test */
    public function it_can_create_a_billing_event()
    {
        $project = Project::factory()->create();
        $eventData = [
            'project_id' => $project->id,
            'name' => 'Facture #001',
            'description' => 'Première facture du projet',
            'type' => EventCategory::Invoice->value,
            'event_type' => EventType::Billing->value,
            'status' => EventStatus::ToSend->value,
            'amount' => 5000.00,
            'payment_status' => PaymentStatus::Pending->value,
            'created_date' => now()->subDays(3),
            'send_date' => now()->addDay(),
            'payment_due_date' => now()->addDays(30),
        ];

        $event = Event::create($eventData);

        $this->assertInstanceOf(Event::class, $event);
        $this->assertEquals('Facture #001', $event->name);
        $this->assertEquals(EventType::Billing->value, $event->event_type);
        $this->assertEquals(EventStatus::ToSend->value, $event->status);
        $this->assertEquals(5000.00, $event->amount);
        $this->assertEquals(PaymentStatus::Pending->value, $event->payment_status);
    }

    /** @test */
    public function it_belongs_to_a_project()
    {
        $project = Project::factory()->create();
        $event = Event::factory()->create(['project_id' => $project->id]);

        $this->assertInstanceOf(Project::class, $event->project);
        $this->assertEquals($project->id, $event->project->id);
    }

    /** @test */
    public function it_has_step_scope()
    {
        Event::factory()->count(3)->step()->create();
        Event::factory()->count(2)->billing()->create();

        $stepEvents = Event::step()->get();

        $this->assertCount(3, $stepEvents);
        $stepEvents->each(function ($event) {
            $this->assertEquals(EventType::Step->value, $event->event_type);
        });
    }

    /** @test */
    public function it_has_billing_scope()
    {
        Event::factory()->count(2)->step()->create();
        Event::factory()->count(4)->billing()->create();

        $billingEvents = Event::billing()->get();

        $this->assertCount(4, $billingEvents);
        $billingEvents->each(function ($event) {
            $this->assertEquals(EventType::Billing->value, $event->event_type);
        });
    }

    /** @test */
    public function it_has_todo_scope_for_step_events()
    {
        Event::factory()->step()->create(['status' => EventStatus::Todo->value]);
        Event::factory()->step()->create(['status' => EventStatus::Done->value]);
        Event::factory()->billing()->create(['status' => EventStatus::ToSend->value]);

        $todoEvents = Event::todo()->get();

        $this->assertCount(1, $todoEvents);
        $this->assertEquals(EventStatus::Todo->value, $todoEvents->first()->status);
    }

    /** @test */
    public function it_has_done_scope_for_step_events()
    {
        Event::factory()->step()->create(['status' => EventStatus::Done->value]);
        Event::factory()->step()->create(['status' => EventStatus::Todo->value]);
        Event::factory()->step()->create(['status' => EventStatus::Done->value]);

        $doneEvents = Event::done()->get();

        $this->assertCount(2, $doneEvents);
        $doneEvents->each(function ($event) {
            $this->assertEquals(EventStatus::Done->value, $event->status);
        });
    }

    /** @test */
    public function it_has_to_send_scope_for_billing_events()
    {
        Event::factory()->billing()->create(['status' => EventStatus::ToSend->value]);
        Event::factory()->billing()->create(['status' => EventStatus::Sent->value]);
        Event::factory()->billing()->create(['status' => EventStatus::ToSend->value]);

        $toSendEvents = Event::toSend()->get();

        $this->assertCount(2, $toSendEvents);
        $toSendEvents->each(function ($event) {
            $this->assertEquals(EventStatus::ToSend->value, $event->status);
        });
    }

    /** @test */
    public function it_has_sent_scope_for_billing_events()
    {
        Event::factory()->billing()->create(['status' => EventStatus::Sent->value]);
        Event::factory()->billing()->create(['status' => EventStatus::ToSend->value]);

        $sentEvents = Event::sent()->get();

        $this->assertCount(1, $sentEvents);
        $this->assertEquals(EventStatus::Sent->value, $sentEvents->first()->status);
    }

    /** @test */
    public function it_has_cancelled_scope()
    {
        Event::factory()->step()->create(['status' => EventStatus::Cancelled->value]);
        Event::factory()->billing()->create(['status' => EventStatus::Cancelled->value]);
        Event::factory()->step()->create(['status' => EventStatus::Todo->value]);

        $cancelledEvents = Event::cancelled()->get();

        $this->assertCount(2, $cancelledEvents);
        $cancelledEvents->each(function ($event) {
            $this->assertEquals(EventStatus::Cancelled->value, $event->status);
        });
    }

    /** @test */
    public function it_has_paid_scope_for_billing_events()
    {
        Event::factory()->billing()->create([
            'status' => EventStatus::Sent->value,
            'payment_status' => PaymentStatus::Paid->value,
        ]);
        Event::factory()->billing()->create([
            'status' => EventStatus::Sent->value,
            'payment_status' => PaymentStatus::Pending->value,
        ]);

        $paidEvents = Event::paid()->get();

        $this->assertCount(1, $paidEvents);
        $this->assertEquals(PaymentStatus::Paid->value, $paidEvents->first()->payment_status);
    }

    /** @test */
    public function it_has_unpaid_scope_for_billing_events()
    {
        Event::factory()->billing()->create([
            'status' => EventStatus::Sent->value,
            'payment_status' => PaymentStatus::Pending->value,
        ]);
        Event::factory()->billing()->create([
            'status' => EventStatus::Sent->value,
            'payment_status' => PaymentStatus::Paid->value,
        ]);
        Event::factory()->billing()->create([
            'status' => EventStatus::ToSend->value,
            'payment_status' => null,
        ]);

        $unpaidEvents = Event::unpaid()->get();

        $this->assertCount(1, $unpaidEvents);
        $this->assertEquals(PaymentStatus::Pending->value, $unpaidEvents->first()->payment_status);
    }

    /** @test */
    public function it_has_overdue_scope_for_step_events()
    {
        Event::factory()->step()->create([
            'status' => EventStatus::Todo->value,
            'execution_date' => now()->subDays(5),
        ]);
        Event::factory()->step()->create([
            'status' => EventStatus::Todo->value,
            'execution_date' => now()->addDays(5),
        ]);
        Event::factory()->step()->create([
            'status' => EventStatus::Done->value,
            'execution_date' => now()->subDays(10),
        ]);

        $overdueEvents = Event::overdue()->get();

        $this->assertCount(1, $overdueEvents);
        $this->assertTrue($overdueEvents->first()->execution_date->isPast());
        $this->assertEquals(EventStatus::Todo->value, $overdueEvents->first()->status);
    }

    /** @test */
    public function it_has_payment_overdue_scope_for_billing_events()
    {
        Event::factory()->billing()->create([
            'status' => EventStatus::Sent->value,
            'payment_status' => PaymentStatus::Pending->value,
            'payment_due_date' => now()->subDays(5),
        ]);
        Event::factory()->billing()->create([
            'status' => EventStatus::Sent->value,
            'payment_status' => PaymentStatus::Pending->value,
            'payment_due_date' => now()->addDays(5),
        ]);
        Event::factory()->billing()->create([
            'status' => EventStatus::Sent->value,
            'payment_status' => PaymentStatus::Paid->value,
            'payment_due_date' => now()->subDays(10),
        ]);

        $paymentOverdueEvents = Event::paymentOverdue()->get();

        $this->assertCount(1, $paymentOverdueEvents);
        $this->assertTrue($paymentOverdueEvents->first()->payment_due_date->isPast());
        $this->assertEquals(PaymentStatus::Pending->value, $paymentOverdueEvents->first()->payment_status);
    }

    /** @test */
    public function it_has_recent_scope()
    {
        Event::factory()->create(['created_at' => now()->subDays(10)]);
        Event::factory()->create(['created_at' => now()->subDays(40)]);
        Event::factory()->create(['created_at' => now()->subDays(5)]);

        $recentEvents = Event::recent(30)->get();

        $this->assertCount(2, $recentEvents);
        $recentEvents->each(function ($event) {
            $this->assertTrue($event->created_at->isAfter(now()->subDays(30)));
        });
    }

    /** @test */
    public function it_has_upcoming_scope()
    {
        Event::factory()->step()->create([
            'status' => EventStatus::Todo->value,
            'execution_date' => now()->addDays(5),
        ]);
        Event::factory()->billing()->create([
            'status' => EventStatus::ToSend->value,
            'send_date' => now()->addDays(3),
        ]);
        Event::factory()->step()->create([
            'status' => EventStatus::Todo->value,
            'execution_date' => now()->subDays(2),
        ]);

        $upcomingEvents = Event::upcoming()->get();

        $this->assertCount(2, $upcomingEvents);
        // Check that the first event is the billing event (sooner)
        $this->assertEquals(EventType::Billing->value, $upcomingEvents->first()->event_type);
    }

    /** @test */
    public function it_detects_if_step_event_is_overdue()
    {
        $overdueEvent = Event::factory()->step()->create([
            'status' => EventStatus::Todo->value,
            'execution_date' => now()->subDays(5),
        ]);

        $futureEvent = Event::factory()->step()->create([
            'status' => EventStatus::Todo->value,
            'execution_date' => now()->addDays(5),
        ]);

        $this->assertTrue($overdueEvent->is_overdue);
        $this->assertFalse($futureEvent->is_overdue);
    }

    /** @test */
    public function it_detects_if_billing_event_payment_is_overdue()
    {
        $overduePayment = Event::factory()->billing()->create([
            'status' => EventStatus::Sent->value,
            'payment_status' => PaymentStatus::Pending->value,
            'payment_due_date' => now()->subDays(5),
        ]);

        $futurePayment = Event::factory()->billing()->create([
            'status' => EventStatus::Sent->value,
            'payment_status' => PaymentStatus::Pending->value,
            'payment_due_date' => now()->addDays(5),
        ]);

        $paidEvent = Event::factory()->billing()->create([
            'status' => EventStatus::Sent->value,
            'payment_status' => PaymentStatus::Paid->value,
            'payment_due_date' => now()->subDays(10),
        ]);

        $this->assertTrue($overduePayment->is_payment_overdue);
        $this->assertFalse($futurePayment->is_payment_overdue);
        $this->assertFalse($paidEvent->is_payment_overdue);
    }

    /** @test */
    public function it_formats_amount_correctly()
    {
        $event = Event::factory()->billing()->create(['amount' => 1234.56]);

        $this->assertEquals('1 234,56 €', $event->formatted_amount);
    }

    /** @test */
    public function it_returns_null_for_step_event_formatted_amount()
    {
        $event = Event::factory()->step()->create();

        $this->assertNull($event->formatted_amount);
    }

    /** @test */
    public function it_casts_dates_properly()
    {
        $event = Event::factory()->create();

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $event->created_date);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $event->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $event->updated_at);
        
        if ($event->execution_date) {
            $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $event->execution_date);
        }
        if ($event->send_date) {
            $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $event->send_date);
        }
        if ($event->payment_due_date) {
            $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $event->payment_due_date);
        }
        if ($event->completed_at) {
            $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $event->completed_at);
        }
        if ($event->paid_at) {
            $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $event->paid_at);
        }
    }

    /** @test */
    public function it_casts_amount_as_decimal()
    {
        $event = Event::factory()->billing()->create(['amount' => 9876.54]);

        $this->assertEquals(9876.54, $event->amount);
        // decimal:2 cast returns a string in Laravel
        $this->assertIsNumeric($event->amount);
    }

    /** @test */
    public function it_automatically_sets_completed_at_when_step_is_done()
    {
        $event = Event::factory()->step()->create([
            'status' => EventStatus::Todo->value,
            'completed_at' => null
        ]);
        
        $this->assertNull($event->completed_at);

        $event->status = EventStatus::Done->value;
        $event->save();

        $this->assertNotNull($event->fresh()->completed_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $event->fresh()->completed_at);
    }

    /** @test */
    public function it_automatically_sets_completed_at_when_billing_is_sent()
    {
        $event = Event::factory()->billing()->create([
            'status' => EventStatus::ToSend->value,
            'completed_at' => null
        ]);
        
        $this->assertNull($event->completed_at);

        $event->status = EventStatus::Sent->value;
        $event->save();

        $this->assertNotNull($event->fresh()->completed_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $event->fresh()->completed_at);
    }

    /** @test */
    public function it_validates_dates_are_after_project_start_date()
    {
        $project = Project::factory()->create([
            'start_date' => now()->subMonths(2),
        ]);

        // This should work - dates after project start
        $validEvent = Event::factory()->create([
            'project_id' => $project->id,
            'created_date' => now()->subMonth(),
            'execution_date' => now(),
        ]);

        $this->assertDatabaseHas('events', ['id' => $validEvent->id]);
    }

    /** @test */
    public function it_can_update_event_status()
    {
        $event = Event::factory()->step()->create(['status' => EventStatus::Todo->value]);

        $event->update(['status' => EventStatus::Done->value]);

        $this->assertEquals(EventStatus::Done->value, $event->fresh()->status);
        $this->assertNotNull($event->fresh()->completed_at);
    }

    /** @test */
    public function it_can_update_payment_status()
    {
        $event = Event::factory()->billing()->create([
            'status' => EventStatus::Sent->value,
            'payment_status' => PaymentStatus::Pending->value,
        ]);

        $event->update([
            'payment_status' => PaymentStatus::Paid->value,
            'paid_at' => now(),
        ]);

        $this->assertEquals(PaymentStatus::Paid->value, $event->fresh()->payment_status);
        $this->assertNotNull($event->fresh()->paid_at);
    }

    /** @test */
    public function it_can_delete_an_event()
    {
        $event = Event::factory()->create();
        $eventId = $event->id;

        $event->delete();

        $this->assertDatabaseMissing('events', ['id' => $eventId]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $event = new Event();
        $fillable = [
            'project_id', 'name', 'description', 'type', 'event_type',
            'status', 'amount', 'payment_status', 'created_date',
            'execution_date', 'send_date', 'payment_due_date',
            'completed_at', 'paid_at'
        ];

        $this->assertEquals($fillable, $event->getFillable());
    }

    /** @test */
    public function it_orders_events_by_created_at_desc_by_default()
    {
        $oldEvent = Event::factory()->create(['created_at' => now()->subDays(5)]);
        $newEvent = Event::factory()->create(['created_at' => now()]);
        $middleEvent = Event::factory()->create(['created_at' => now()->subDays(2)]);

        $events = Event::all();

        $this->assertEquals($newEvent->id, $events[0]->id);
        $this->assertEquals($middleEvent->id, $events[1]->id);
        $this->assertEquals($oldEvent->id, $events[2]->id);
    }

    /** @test */
    public function it_differentiates_step_and_billing_events_correctly()
    {
        $stepEvent = Event::factory()->step()->create();
        $billingEvent = Event::factory()->billing()->create();

        // Step event should have step-specific fields
        $this->assertEquals(EventType::Step->value, $stepEvent->event_type);
        $this->assertNotNull($stepEvent->execution_date);
        $this->assertNull($stepEvent->amount);
        $this->assertNull($stepEvent->payment_status);
        $this->assertNull($stepEvent->send_date);
        $this->assertNull($stepEvent->payment_due_date);

        // Billing event should have billing-specific fields
        $this->assertEquals(EventType::Billing->value, $billingEvent->event_type);
        $this->assertNotNull($billingEvent->amount);
        $this->assertNotNull($billingEvent->send_date);
        $this->assertNull($billingEvent->execution_date);
    }
}