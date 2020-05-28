<?php

namespace App\Repositories\Eloquent;


use App\Models\Invitation;
use App\Repositories\Contacts\InvitationInterface;
use App\Repositories\Contacts\UserInterface;

class InvitationRepositories extends BaseRepository implements InvitationInterface
{
    public function model()
    {
        return Invitation::class;
    }

    public function addUserToTeam($team, $user_id)
    {
        $team->members()->attach($user_id);
    }

    public function removeUserFromTeam($team, $user_id)
    {
        $team->members()->detach($user_id);
    }
}