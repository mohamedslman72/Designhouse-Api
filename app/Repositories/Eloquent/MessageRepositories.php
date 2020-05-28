<?php

namespace App\Repositories\Eloquent;
use App\Models\Message;
use App\Repositories\Contacts\MessageInterface;

class MessageRepositories extends BaseRepository implements MessageInterface
{
    public function model()
    {
        return Message::class;
    }
}