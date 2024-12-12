<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\BorrowRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Faker\Generator as Faker;
use Illuminate\Foundation\Testing\WithFaker;

class BorrowRecordControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker; // Add WithFaker trait

    public function test_authenticated_user_can_access_borrow_records()
    {
        // Create a user with either 'Admin' or 'Librarian' role
        $user = User::factory()->create([
            'role' => $this->faker->randomElement(['Admin', 'Librarian']),
        ]);

        // Authenticate the user using Sanctum
        Sanctum::actingAs($user, ['*']);

        // Create some borrow records
        BorrowRecord::factory()->count(5)->create();

        // Perform the GET request
        $response = $this->getJson('/api/borrow-records');

        // Assert that the request was successful
        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_view_specific_borrow_record()
    {
        // Create a user with either 'Admin' or 'Librarian' role
        $user = User::factory()->create([
            'role' => $this->faker->randomElement(['Admin', 'Librarian']),
        ]);

        // Authenticate the user using Sanctum
        Sanctum::actingAs($user, ['*']);

        // Create a borrow record
        $borrowRecord = BorrowRecord::factory()->create();

        // Perform the GET request for a specific record
        $response = $this->getJson("/api/borrow-records/{$borrowRecord->id}");

        // Assert that the request was successful
        $response->assertStatus(200);
    }

    public function test_viewing_non_existent_borrow_record_returns_404()
    {
        // Create a user with either 'Admin' or 'Librarian' role
        $user = User::factory()->create([
            'role' => $this->faker->randomElement(['Admin', 'Librarian']),
        ]);

        // Authenticate the user using Sanctum
        Sanctum::actingAs($user, ['*']);

        // Perform the GET request for a non-existent record
        $response = $this->getJson("/api/borrow-records/999");

        // Assert that the response returns a 404 error
        $response->assertStatus(404);
    }
}
