<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Fine;
use App\Models\BorrowRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;

class FineControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        // Any additional setup can be done here
    }

    public function test_authenticated_user_can_view_fines()
    {
        $user = User::factory()->create([
            'role' => $this->faker->randomElement(['Admin', 'Librarian']),
        ]);

        Sanctum::actingAs($user, ['*']);

        // Create fines
        Fine::factory()->count(5)->create();

        // Perform GET request
        $response = $this->getJson('/api/fines');

        // Assert successful response
        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_view_specific_fine()
    {
        $user = User::factory()->create([
            'role' => $this->faker->randomElement(['Admin', 'Librarian']),
        ]);

        Sanctum::actingAs($user, ['*']);

        // Create a fine
        $fine = Fine::factory()->create();

        // Perform GET request
        $response = $this->getJson("/api/fines/{$fine->id}");

        // Assert successful response
        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_calculate_fine_for_borrow_record()
    {
        $user = User::factory()->create([
            'role' => $this->faker->randomElement(['Admin', 'Librarian']),
        ]);

        Sanctum::actingAs($user, ['*']);

        // Create a borrow record that is overdue
        $borrowRecord = BorrowRecord::factory()->create([
            'due_at' => Carbon::now()->subDays(5),
            'returned_at' => Carbon::now(),
        ]);

        // Perform POST request to calculate fine
        $response = $this->postJson("/api/borrow-records/{$borrowRecord->id}/calculate-fine");

        // Assert fine is created successfully
        $response->assertStatus(201);
    }

    public function test_no_fine_applicable_if_returned_on_time()
    {
        $user = User::factory()->create([
            'role' => $this->faker->randomElement(['Admin', 'Librarian']),
        ]);

        Sanctum::actingAs($user, ['*']);

        // Create a borrow record that is returned on time
        $borrowRecord = BorrowRecord::factory()->create([
            'due_at' => Carbon::now(),
            'returned_at' => Carbon::now(),
        ]);

        // Perform POST request to calculate fine
        $response = $this->postJson("/api/borrow-records/{$borrowRecord->id}/calculate-fine");

        // Assert no fine is applicable
        $response->assertStatus(200)->assertJson(['message' => 'No fine applicable']);
    }

    public function test_authenticated_user_can_pay_fine()
    {
        $user = User::factory()->create([
            'role' => 'Member',
        ]);
    
        Sanctum::actingAs($user, ['*']);
    
        // Create a fine with 'paid' set to false
        $fine = Fine::factory()->create(['paid' => false]);
    
        // Perform PATCH request to pay the fine
        $response = $this->patchJson("/api/fines/{$fine->id}/pay");
    
        // Debugging: output response for troubleshooting
        $response->dump();
    
        // Assert the status code is 200 (success)
        $response->assertStatus(200);
    
        // Assert the fine is marked as paid (1)
        $this->assertEquals(1, $fine->fresh()->paid);  // Check if it's 1 (true in the database)
    }
    

    public function test_non_existent_fine_returns_404()
    {
        $user = User::factory()->create([
            'role' => $this->faker->randomElement(['Admin', 'Librarian']),
        ]);

        Sanctum::actingAs($user, ['*']);

        // Perform GET request for non-existent fine
        $response = $this->getJson("/api/fines/9999");

        // Assert 404 response
        $response->assertStatus(404);
    }

    public function test_unauthorized_user_cannot_access_fines()
    {
        // Create a user with 'Member' role
        $user = User::factory()->create(['role' => 'Member']);

        Sanctum::actingAs($user, ['*']);

        // Perform GET request
        $response = $this->getJson('/api/fines');

        // Assert unauthorized access
        $response->assertStatus(403); // You may need to update your authorization to return a 403
    }
}
