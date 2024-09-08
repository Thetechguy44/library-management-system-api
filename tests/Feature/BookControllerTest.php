<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\Author;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class BookControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'role'=>$this->faker->randomElement(['Admin', 'Librarian', 'Member'])
        ]);
    }

    public function test_index_returns_paginated_books()
    {
        Sanctum::actingAs($this->user);
        Book::factory()->count(20)->create();

        $response = $this->getJson('/api/books');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta'
            ])
            ->assertJsonCount(15, 'data');
    }

    public function test_index_can_search_books()
    {
        Sanctum::actingAs($this->user);
        $searchBook = Book::factory()->create(['title' => 'Unique Title']);
        Book::factory()->count(5)->create();

        $response = $this->getJson('/api/books?search=Unique');

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Unique Title']);
    }

    public function test_show_returns_a_book()
    {
        Sanctum::actingAs($this->user);
        $book = Book::factory()->create();

        $response = $this->getJson("/api/books/{$book->id}");

        $response->assertStatus(200)
            ->assertJson($book->toArray());
    }

    public function test_store_creates_a_new_book()
    {
        Sanctum::actingAs($this->user);
        $author = Author::factory()->create();
        $bookData = [
            'title' => $this->faker->sentence,
            'isbn' => $this->faker->isbn13,
            'published_date' => $this->faker->date,
            'author_id' => $author->id,
            'status' => 'Available',
        ];

        $response = $this->postJson('/api/books', $bookData);

        $response->assertStatus(201)
            ->assertJsonFragment($bookData);

        $this->assertDatabaseHas('books', $bookData);
    }

    public function test_update_modifies_a_book()
    {
        Sanctum::actingAs($this->user);
        $book = Book::factory()->create();
        $newData = [
            'title' => 'Updated Title',
            'status' => 'Borrowed',
        ];

        $response = $this->putJson("/api/books/{$book->id}", $newData);

        $response->assertStatus(200)
            ->assertJsonFragment($newData);

        $this->assertDatabaseHas('books', array_merge(['id' => $book->id], $newData));
    }

    public function test_destroy_deletes_a_book()
    {
        Sanctum::actingAs($this->user);
        $book = Book::factory()->create();

        $response = $this->deleteJson("/api/books/{$book->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Book deleted successfully']);

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    public function test_borrow_book()
    {
        Sanctum::actingAs($this->user);
        $book = Book::factory()->create(['status' => 'Available']);

        $response = $this->postJson("/api/books/{$book->id}/borrow");

        $response->assertStatus(201)
            ->assertJsonStructure(['user_id', 'borrowed_at', 'due_at']);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'status' => 'Borrowed'
        ]);
    }

    public function test_return_book()
    {
        Sanctum::actingAs($this->user);
        $book = Book::factory()->create(['status' => 'Borrowed']);
        $book->borrowRecords()->create([
            'user_id' => $this->user->id,
            'borrowed_at' => now(),
            'due_at' => now()->addDays(14),
        ]);

        $response = $this->postJson("/api/books/{$book->id}/return");

        $response->assertStatus(200)
            ->assertJsonStructure(['returned_at']);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'status' => 'Available'
        ]);
    }
}