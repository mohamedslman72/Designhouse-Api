<?php

namespace App\Repositories\Eloquent;
use App\Models\Comment;
use App\Repositories\Contacts\CommentInterface;

class CommentRepositories extends BaseRepository implements CommentInterface
{
    public function model()
    {
        return Comment::class;
    }
}