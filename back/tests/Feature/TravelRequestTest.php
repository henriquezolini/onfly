<?php

namespace Tests\Feature;

use App\Models\TravelRequest;
use App\Models\User;
use App\Notifications\TravelRequestStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TravelRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_request()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/travel-requests', [
                             'destination' => 'Paris',
                             'departure_date' => '2023-10-01',
                             'return_date' => '2023-10-10',
                         ]);

        $response->assertStatus(201)
                 ->assertJson(['destination' => 'Paris']);

        $this->assertDatabaseHas('travel_requests', ['destination' => 'Paris']);
    }

    public function test_user_can_view_own_requests()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        
        TravelRequest::factory()->create(['user_id' => $user->id]);
        TravelRequest::factory()->create(['user_id' => $otherUser->id]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/travel-requests');

        $response->assertStatus(200)
                 ->assertJsonCount(1);
    }

    public function test_admin_can_view_all_requests()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        
        TravelRequest::factory()->create(['user_id' => $user->id]);
        TravelRequest::factory()->create(['user_id' => $admin->id]);

        $token = $admin->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/travel-requests');

        $response->assertStatus(200)
                 ->assertJsonCount(2);
    }

    public function test_admin_can_update_status()
    {
        Notification::fake();

        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        $request = TravelRequest::factory()->create(['user_id' => $user->id, 'status' => 'requested']);

        $token = $admin->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->patchJson("/api/travel-requests/{$request->id}/status", [
                             'status' => 'approved'
                         ]);

        $response->assertStatus(200)
                 ->assertJson(['status' => 'approved']);

        Notification::assertSentTo(
            [$user],
            TravelRequestStatusChanged::class
        );
    }

    public function test_user_cannot_update_status()
    {
        $user = User::factory()->create();
        $request = TravelRequest::factory()->create(['user_id' => $user->id, 'status' => 'requested']);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->patchJson("/api/travel-requests/{$request->id}/status", [
                             'status' => 'approved'
                         ]);

        $response->assertStatus(403);
    }
}
