<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ClientControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_displays_clients_index_page()
    {
        Client::factory()->count(5)->create();

        $response = $this->actingAs($this->user)
            ->get(route('clients.index'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Clients/Index')
                ->has('clients.data', 5)
                ->has('clients.data.0', fn (Assert $page) => $page
                    ->hasAll(['id', 'name', 'email', 'phone', 'company'])
                )
            );
    }

    /** @test */
    public function it_displays_create_client_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('clients.create'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Clients/Create')
            );
    }

    /** @test */
    public function it_can_store_a_new_client()
    {
        $clientData = [
            'name' => 'Jean Dupont',
            'email' => 'jean.dupont@example.com',
            'phone' => '06 12 34 56 78',
            'company' => 'Dupont SARL',
            'address' => '123 rue de la République',
            'notes' => 'Client important'
        ];

        $response = $this->actingAs($this->user)
            ->post(route('clients.store'), $clientData);

        $response->assertRedirect(route('clients.index'));
        $response->assertSessionHas('message', 'Client créé avec succès.');
        
        $this->assertDatabaseHas('clients', [
            'name' => 'Jean Dupont',
            'email' => 'jean.dupont@example.com'
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_client()
    {
        $response = $this->actingAs($this->user)
            ->post(route('clients.store'), []);

        $response->assertSessionHasErrors(['name', 'email']);
    }

    /** @test */
    public function it_validates_email_format()
    {
        $clientData = [
            'name' => 'Test Client',
            'email' => 'invalid-email'
        ];

        $response = $this->actingAs($this->user)
            ->post(route('clients.store'), $clientData);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function it_displays_client_details()
    {
        $client = Client::factory()->create();
        Project::factory()->count(3)->create(['client_id' => $client->id]);

        $response = $this->actingAs($this->user)
            ->get(route('clients.show', $client));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Clients/Show')
                ->has('client', fn (Assert $page) => $page
                    ->where('id', $client->id)
                    ->where('name', $client->name)
                    ->hasAll(['email', 'phone', 'company', 'address', 'notes'])
                )
                ->has('projects', 3)
                ->has('stats')
            );
    }

    /** @test */
    public function it_displays_edit_client_page()
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('clients.edit', $client));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Clients/Edit')
                ->has('client', fn (Assert $page) => $page
                    ->where('id', $client->id)
                    ->hasAll(['name', 'email', 'phone', 'company', 'address', 'notes'])
                )
            );
    }

    /** @test */
    public function it_can_update_a_client()
    {
        $client = Client::factory()->create([
            'name' => 'Old Name'
        ]);

        $updatedData = [
            'name' => 'New Name',
            'email' => 'new.email@example.com',
            'phone' => '07 98 76 54 32',
            'company' => 'New Company',
            'address' => 'New Address',
            'notes' => 'Updated notes'
        ];

        $response = $this->actingAs($this->user)
            ->put(route('clients.update', $client), $updatedData);

        $response->assertRedirect(route('clients.index'));
        $response->assertSessionHas('message', 'Client mis à jour avec succès.');
        
        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'New Name',
            'email' => 'new.email@example.com'
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_updating_client()
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->user)
            ->put(route('clients.update', $client), [
                'name' => '',
                'email' => ''
            ]);

        $response->assertSessionHasErrors(['name', 'email']);
    }

    /** @test */
    public function it_can_delete_a_client()
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('clients.destroy', $client));

        $response->assertRedirect(route('clients.index'));
        $response->assertSessionHas('message', 'Client supprimé avec succès.');
        
        $this->assertDatabaseMissing('clients', [
            'id' => $client->id
        ]);
    }

    /** @test */
    public function it_requires_authentication_to_access_clients()
    {
        $response = $this->get(route('clients.index'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('clients.create'));
        $response->assertRedirect(route('login'));

        $client = Client::factory()->create();
        $response = $this->get(route('clients.show', $client));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_paginates_clients_list()
    {
        Client::factory()->count(25)->create();

        $response = $this->actingAs($this->user)
            ->get(route('clients.index'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Clients/Index')
                ->has('clients.data', 10) // Assuming default pagination is 10
                ->has('clients.links')
                ->has('clients.meta')
            );
    }

    /** @test */
    public function it_searches_clients_by_name()
    {
        Client::factory()->create(['name' => 'Jean Dupont']);
        Client::factory()->create(['name' => 'Marie Martin']);
        Client::factory()->create(['name' => 'Pierre Bernard']);

        $response = $this->actingAs($this->user)
            ->get(route('clients.index', ['search' => 'Jean']));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Clients/Index')
                ->has('clients.data', 1)
                ->where('clients.data.0.name', 'Jean Dupont')
            );
    }

    /** @test */
    public function it_calculates_client_statistics_correctly()
    {
        $client = Client::factory()->create();
        
        // Create projects with different statuses
        Project::factory()->create([
            'client_id' => $client->id,
            'status' => 'active'
        ]);
        Project::factory()->create([
            'client_id' => $client->id,
            'status' => 'completed'
        ]);
        Project::factory()->create([
            'client_id' => $client->id,
            'status' => 'on_hold'
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('clients.show', $client));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Clients/Show')
                ->where('stats.total_projects', 3)
                ->where('stats.active_projects', 1)
                ->where('stats.completed_projects', 1)
            );
    }
}