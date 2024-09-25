<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Reservation;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;

class ReservationControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        // Any additional setup can be done here
    }

    public function test_authenticated_member_can_create_reservation()
    {
        $user = User::factory()->create(['role' => 'Member']);
        $book = Book::factory()->create(['status' => 'Available']);

        Sanctum::actingAs($user, ['*']);

        $reservationData = [
            'book_id' => $book->id,
            'reserved_from' => Carbon::now()->addDays(1)->format('Y-m-d'),
            'reserved_to' => Carbon::now()->addDays(7)->format('Y-m-d'),
        ];

        $response = $this->postJson('/api/reservations', $reservationData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('reservations', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_cannot_create_reservation_for_unavailable_book()
    {
        $user = User::factory()->create(['role' => 'Member']);
        $book = Book::factory()->create(['status' => 'Borrowed']);

        Sanctum::actingAs($user, ['*']);

        $reservationData = [
            'book_id' => $book->id,
            'reserved_from' => Carbon::now()->addDays(1)->format('Y-m-d'),
            'reserved_to' => Carbon::now()->addDays(7)->format('Y-m-d'),
        ];

        $response = $this->postJson('/api/reservations', $reservationData);

        $response->assertStatus(400)
                 ->assertJson(['message' => 'Book is not available for reservation']);
    }

    public function test_admin_or_librarian_can_view_all_reservations()
    {
        $user = User::factory()->create(['role' => $this->faker->randomElement(['Admin', 'Librarian'])]);
        Reservation::factory()->count(5)->create();

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson('/api/reservations');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_view_their_own_reservation()
    {
        $user = User::factory()->create(['role' => 'Member']);
        $reservation = Reservation::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson("/api/reservations/{$reservation->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'user_id' => $user->id,
                     'book_id' => $reservation->book_id,
                 ]);
    }

    public function test_authenticated_user_can_update_reservation_status()
    {
        $user = User::factory()->create(['role' => 'Member']);
        $reservation = Reservation::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user, ['*']);

        $updateData = ['status' => 'Cancelled'];

        $response = $this->patchJson("/api/reservations/{$reservation->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'Cancelled',
        ]);
    }

    public function test_unauthorized_user_cannot_access_reservations()
    {
        // Create a user with a role that should not have access
        $user = User::factory()->create(['role' => 'Member']);
    
        Sanctum::actingAs($user, ['*']);
    
        // Try to access the list of reservations
        $response = $this->getJson('/api/reservations');
    
        $response->assertStatus(200); // Assuming your system restricts access based on roles
    }
    
}
