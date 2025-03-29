<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Message; 
use App\Services\MessageService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_send_message()
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $messageService = $this->app->make(MessageService::class);
        
        $message = $messageService->sendMessage(
            $sender->id, 
            $receiver->id, 
            'Hello, test message'
        );

        $this->assertDatabaseHas('messages', [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => 'Hello, test message',
            'status' => 'sent'
        ]);
    }

    public function test_retrieve_user_messages()
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        // Create some test messages
        Message::factory()->count(5)->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id
        ]);

        $response = $this->actingAs($receiver)
            ->getJson("/api/chat/messages/{$sender->id}");

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function test_mark_message_as_read()
    {
        $message = Message::factory()->create();

        $response = $this->actingAs($message->receiver)
            ->patchJson("/api/chat/read/{$message->id}");

        $response->assertStatus(200);
        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'status' => 'read'
        ]);
    }
}
