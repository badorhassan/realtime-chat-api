<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_message()
    {
        $user = User::factory()->create();
        $receiver = User::factory()->create();
        $token = auth()->login($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/chat/send', [
                'receiver_id' => $receiver->id,
                'message' => 'Hello!',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('messages', [
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
            'message' => 'Hello!',
        ]);
    }
}