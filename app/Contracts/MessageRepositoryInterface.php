<?php

namespace App\Contracts;

interface MessageRepositoryInterface
{
    public function store(array $data);
    public function getMessagesForUser(int $userId, int $page);
    public function markAsRead(int $messageId);
}