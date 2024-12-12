<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class ReviewControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_authenticated_user_can_create_review()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $reviewData = [
            'comment' => $this->faker->sentence,
            'rating' => 5
        ];

        $response = $this->postJson("/api/books/{$book->id}/reviews", $reviewData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'comment' => $reviewData['comment'],
            'rating' => $reviewData['rating']
        ]);
    }

    public function test_authenticated_user_can_view_reviews_for_a_book()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        Review::factory()->count(3)->create(['book_id' => $book->id]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson("/api/books/{$book->id}/reviews");

        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => [['id', 'comment', 'rating', 'user_id', 'book_id']]]);
    }

    public function test_authenticated_user_can_update_their_own_review()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user, ['*']);

        $updatedData = [
            'comment' => 'Updated comment',
            'rating' => 4
        ];

        $response = $this->patchJson("/api/reviews/{$review->id}", $updatedData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'comment' => $updatedData['comment'],
            'rating' => $updatedData['rating']
        ]);
    }

    public function test_only_review_owner_can_delete_their_review()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->deleteJson("/api/reviews/{$review->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    public function test_user_cannot_update_other_users_review()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $otherUser->id]);

        Sanctum::actingAs($user, ['*']);

        $updatedData = [
            'comment' => 'Unauthorized comment update',
            'rating' => 2
        ];

        $response = $this->patchJson("/api/reviews/{$review->id}", $updatedData);

        $response->assertStatus(403);  // Forbidden
    }

    public function test_unauthenticated_user_cannot_create_or_modify_review()
    {
        $book = Book::factory()->create();
        $review = Review::factory()->create(['book_id' => $book->id]);

        $reviewData = [
            'comment' => 'New Review',
            'rating' => 5
        ];

        // Attempt to create a review
        $responseCreate = $this->postJson("/api/books/{$book->id}/reviews", $reviewData);
        $responseCreate->assertStatus(401);  // Unauthenticated

        // Attempt to update a review
        $responseUpdate = $this->patchJson("/api/reviews/{$review->id}", $reviewData);
        $responseUpdate->assertStatus(401);  // Unauthenticated
    }

    public function test_unauthenticated_user_cannot_delete_review()
    {
        $review = Review::factory()->create();

        // Attempt to delete the review
        $response = $this->deleteJson("/api/reviews/{$review->id}");

        $response->assertStatus(401);  // Unauthenticated
    }
}
