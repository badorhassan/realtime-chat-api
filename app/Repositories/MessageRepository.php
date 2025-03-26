<?php

namespace App\Repositories;

use App\Contracts\MessageRepositoryInterface;
use App\Models\Message;

class MessageRepository implements MessageRepositoryInterface
{
    public function store(array $data)
    {
         $data['status'] = $data['status'] ?? 'sent';
        return Message::create($data);
    }
   
    public function getMessagesForUser(int $userId, int $page)
    {
        return Message::where('receiver_id', $userId)
            ->orWhere('sender_id', $userId)
            ->latest()
            ->paginate(10, ['*'], 'page', $page);
    }

    public function markAsRead(int $messageId)
    {
       
        $message = Message::findOrFail($messageId);
         $message->update(['status' => 'read']);
         return $message;
    }
}