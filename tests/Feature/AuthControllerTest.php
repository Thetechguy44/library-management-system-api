<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_logout_successfully()
    {
        // Create a user
        $user = User::factory()->create();

        // Simulate user authentication using Sanctum
        Sanctum::actingAs($user, ['*']);

        // Make a logout request
        $response = $this->postJson('/api/logout');

        // Assert the response is successful (HTTP status 200)
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logged out successfully'
            ]);

        // Assert that the token was revoked (optional)
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);
    }
}
