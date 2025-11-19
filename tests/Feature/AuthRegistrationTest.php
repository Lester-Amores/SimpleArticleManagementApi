<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user registration with valid data
     */
    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
            ])
            ->assertJson([
                'message' => 'User registered successfully',
                'user' => [
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);
    }

    /**
     * Test user registration fails with duplicate email
     */
    public function test_user_registration_fails_with_duplicate_email(): void
    {
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test user registration fails with invalid email
     */
    public function test_user_registration_fails_with_invalid_email(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test user registration fails with short password
     */
    public function test_user_registration_fails_with_short_password(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test user registration fails with missing required fields
     */
    public function test_user_registration_fails_with_missing_fields(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            // Missing email and password
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * Test password is hashed in database
     */
    public function test_password_is_hashed_in_database(): void
    {
        $password = 'password123';

        $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => $password,
        ]);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotEquals($password, $user->password);
        $this->assertTrue(Hash::check($password, $user->password));
    }
}

