<?php
namespace App\Repositories\Contacts;
interface ChatInterface
{
    public function createParticipant($chat_id,array $data);
    public function getUserChats();
}
