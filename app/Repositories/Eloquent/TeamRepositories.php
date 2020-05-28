<?php

namespace App\Repositories\Eloquent;


use App\Models\Team;
use App\Models\User;
use App\Repositories\Contacts\TeamInterface;
use App\Repositories\Contacts\UserInterface;

class TeamRepositories extends BaseRepository implements TeamInterface
{
    public function model()
    {
        return Team::class;
    }

    public function fetchUserTeams()
    {
        return auth()->user()->teams;
    }
}