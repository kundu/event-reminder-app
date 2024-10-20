<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class AuthRegisterTest
 *
 * This class contains tests for user registration functionality.
 */
class AuthRegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that registration requires a name.
     *
     * @return void
     */
    public function test_registration_requires_name()
    {
        $userData = [
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseMissing('users', ['email' => 'testuser@example.com']);
    }

    /**
     * Test that registration requires an email.
     *
     * @return void
     */
    public function test_registration_requires_email()
    {
        $userData = [
            'name' => 'Test User',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('users', ['name' => 'Test User']);
    }

    /**
     * Test that registration requires a valid email.
     *
     * @return void
     */
    public function test_registration_requires_valid_email()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('users', ['email' => 'invalid-email']);
    }

    /**
     * Test that registration requires a password.
     *
     * @return void
     */
    public function test_registration_requires_password()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'testuser@example.com']);
    }

    /**
     * Test that registration passwords must match.
     *
     * @return void
     */
    public function test_registration_passwords_must_match()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
        ];

        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'testuser@example.com']);
    }

    /**
     * Test that registration fails with a duplicate email.
     *
     * @return void
     */
    public function test_registration_fails_with_duplicate_email()
    {
        User::factory()->create(['email' => 'testuser@example.com']);

        $userData = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 1); // Ensure only one user exists
    }
}
