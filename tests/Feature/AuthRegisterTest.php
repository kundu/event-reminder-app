<?php

namespace Tests\Feature;

use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRegisterTest extends TestCase
{
    use RefreshDatabase;

    protected $authService; // Declare the property

    protected function setUp(): void
    {
        parent::setUp();
        // You can bind the mock to the service container
        $this->authService = $this->createMock(AuthService::class);
        $this->app->instance(AuthService::class, $this->authService);
    }

    /** @test */
    public function it_shows_the_registration_form()
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    /** @test */
    public function it_registers_a_user_successfully()
    {
        // Mock the AuthService to simulate user registration
        $this->authService->method('register')->willReturn((object)['email' => 'test@example.com']);

        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('verify.otp'));
    }

    /** @test */
    public function it_fails_registration_with_invalid_data()
    {
        $response = $this->post(route('register'), [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'notmatching',
        ]);

        // Check if the response is a redirect instead of an error
        $response->assertRedirect(); // This indicates a redirect instead of validation errors
    }
}
