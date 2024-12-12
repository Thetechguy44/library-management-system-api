<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $adminUser;
    protected $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user for authorized actions
        $this->adminUser = User::factory()->create([
            'role' => 'Admin',
        ]);

        // Create a regular user for unauthorized actions
        $this->regularUser = User::factory()->create([
            'role' => 'Member',
        ]);
    }

    public function test_index_returns_paginated_users()
    {
        Sanctum::actingAs(
            User::factory()->create(['role' => 'Admin']) // Set role
        );
        User::factory()->count(20)->create(['role' => 'Member']); // Ensure users have roles
    
        $response = $this->getJson('/api/users');
    
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta'
            ])
            ->assertJsonCount(15, 'data');
    }
    

    public function test_show_returns_a_user()
    {
        Sanctum::actingAs($this->adminUser);
        $user = User::factory()->create();

        $response = $this->getJson("/api/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson($user->toArray());
    }

    public function test_store_creates_a_new_user()
    {
        Sanctum::actingAs(
            User::factory()->create(['role' => 'Admin']) // Set role for the acting user
        );
    
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'password' => 'password123',
            'role' => 'Member',
        ];
    
        $response = $this->postJson('/api/users', $userData);
    
        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => $userData['name'],
                'email' => $userData['email'],
            ]);
    
        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'role' => $userData['role'], // Check role is saved
        ]);
    }
    
    public function test_update_modifies_a_user()
    {
        Sanctum::actingAs($this->adminUser);
        $user = User::factory()->create();
        $newData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'role' => 'Librarian',
        ];

        $response = $this->putJson("/api/users/{$user->id}", $newData);

        $response->assertStatus(200)
            ->assertJsonFragment($newData);

        $this->assertDatabaseHas('users', array_merge(['id' => $user->id], $newData));
    }

    public function test_destroy_deletes_a_user()
    {
        Sanctum::actingAs($this->adminUser);
        $user = User::factory()->create();

        $response = $this->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'User deleted successfully']);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_unauthenticated_user_cannot_access_users()
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(401);
    }

    public function test_non_admin_user_cannot_access_users()
    {
        Sanctum::actingAs($this->regularUser);

        $response = $this->getJson('/api/users');

        $response->assertStatus(403);
    }

    public function test_store_validates_input()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->postJson('/api/users', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password', 'role']);
    }
}
