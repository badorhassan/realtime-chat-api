<?php

namespace App\Services;

use App\Contracts\MessageRepositoryInterface;
use App\Events\MessageSent;

class MessageService
{
    protected $messageRepository;

    public function __construct(MessageRepositoryInterface $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    public function sendMessage($senderId, $receiverId, $message)
    {
        $data = [
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'message' => $message,
            'status' => 'sent',
        ];
        $message = $this->messageRepository->store($data);
        event(new MessageSent($message)); // Trigger event
        return $message;
}

public function getMessages($userId, $page = 1)
{
return $this->messageRepository->getMessagesForUser($userId, $page);
}

public function markMessageAsRead($messageId)
{
    
    return $this->messageRepository->markAsRead($messageId);
}
}