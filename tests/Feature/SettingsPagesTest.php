<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SettingsPagesTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
    }

    public function test_settings_profile_page_loads(): void
    {
        $response = $this->actingAs($this->user)->get('/settings/profile');
        
        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page->component('settings/Profile'));
    }

    public function test_settings_password_page_loads(): void
    {
        $response = $this->actingAs($this->user)->get('/settings/password');
        
        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page->component('settings/Password'));
    }

    public function test_settings_appearance_page_loads(): void
    {
        $response = $this->actingAs($this->user)->get('/settings/appearance');
        
        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page->component('settings/Appearance'));
    }
}