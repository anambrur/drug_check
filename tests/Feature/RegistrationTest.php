<?php

namespace Tests\Feature;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Features;
use Laravel\Jetstream\Jetstream;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is not enabled.');

            return;
        }

        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_registration_screen_cannot_be_rendered_if_support_is_disabled(): void
    {
        if (Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is enabled.');

            return;
        }

        $response = $this->get('/register');

        $response->assertStatus(404);
    }

    public function test_new_users_can_register_as_not_dot(): void
    {
        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is not enabled.');

            return;
        }

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'is_dot' => '0',
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'status' => 1,
            'type' => 2,
        ]);
        $this->assertDatabaseMissing('client_profiles', [
            'der_contact_email' => 'test@example.com',
        ]);
    }

    public function test_new_users_can_register_as_dot(): void
    {
        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is not enabled.');

            return;
        }

        $this->seed(\Database\Seeders\PermissionSeeder::class);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'dot@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'is_dot' => '1',
            'company_name' => 'Test Company',
            'phone' => '555-0100',
            'address' => '123 Main St',
            'city' => 'Austin',
            'state' => 'TX',
            'zip' => '78701',
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
        $this->assertDatabaseHas('users', [
            'email' => 'dot@example.com',
            'status' => 1,
            'type' => 2,
        ]);
        $this->assertDatabaseHas('client_profiles', [
            'company_name' => 'Test Company',
            'status' => 'active',
        ]);
    }
}
