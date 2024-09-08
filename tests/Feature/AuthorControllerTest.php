<?php

namespace Tests\Feature\Api;

use App\Models\Author;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class AuthorControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'role'=>'Admin',
        ]);
    }

    public function test_index_returns_paginated_authors()
    {
        Sanctum::actingAs($this->user);
        Author::factory()->count(20)->create();

        $response = $this->getJson('/api/authors');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta'
            ])
            ->assertJsonCount(15, 'data');
    }

    public function test_show_returns_an_author()
    {
        Sanctum::actingAs($this->user);
        $author = Author::factory()->create();

        $response = $this->getJson("/api/authors/{$author->id}");

        $response->assertStatus(200)
            ->assertJson($author->toArray());
    }

    public function test_store_creates_a_new_author()
    {
        Sanctum::actingAs($this->user);
        $authorData = [
            'name' => $this->faker->name,
            'bio' => $this->faker->paragraph,
            'birthdate' => $this->faker->date,
        ];

        $response = $this->postJson('/api/authors', $authorData);

        $response->assertStatus(201)
            ->assertJsonFragment($authorData);

        $this->assertDatabaseHas('authors', $authorData);
    }

    public function test_update_modifies_an_author()
    {
        Sanctum::actingAs($this->user);
        $author = Author::factory()->create();
        $newData = [
            'name' => 'Updated Name',
            'bio' => 'Updated bio',
        ];

        $response = $this->putJson("/api/authors/{$author->id}", $newData);

        $response->assertStatus(200)
            ->assertJsonFragment($newData);

        $this->assertDatabaseHas('authors', array_merge(['id' => $author->id], $newData));
    }

    public function test_destroy_deletes_an_author()
    {
        Sanctum::actingAs($this->user);
        $author = Author::factory()->create();

        $response = $this->deleteJson("/api/authors/{$author->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Author deleted successfully']);

        $this->assertDatabaseMissing('authors', ['id' => $author->id]);
    }

    public function test_unauthenticated_user_cannot_access_authors()
    {
        $response = $this->getJson('/api/authors');

        $response->assertStatus(401);
    }

    public function test_store_validates_input()
    {
        Sanctum::actingAs($this->user);
        $response = $this->postJson('/api/authors', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }
}