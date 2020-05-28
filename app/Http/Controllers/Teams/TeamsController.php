<?php

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Repositories\Contacts\InvitationInterface;
use App\Repositories\Contacts\TeamInterface;
use App\Repositories\Contacts\UserInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeamsController extends Controller
{
    protected $teams;
    protected $users;
    protected $invitations;

    public function __construct(TeamInterface $teams, UserInterface $users, InvitationInterface $invitations)
    {
        $this->teams = $teams;
        $this->users = $users;
        $this->invitations = $invitations;
    }

    public function index()
    {

    }

    public function store(Request $request)
    {
        $this->validate( $request, [
            'name' => ['required', 'string', 'max:80', 'unique:teams,name']
        ] );
        $team = $this->teams->create( [
            'owner_id' => auth()->id(),
            'name' => $request->name,
            'slug' => Str::slug( $request->name )
        ] );
        $team->members()->attach( ['owner_id' => auth()->id()] );
        return new TeamResource( $team );
    }

    public function update(Request $request, $id)
    {
        $team = $this->teams->find( $id );
        $this->authorize( 'update', $team );
        $this->validate( $request, [
            'name' => ['required', 'string', 'max:80', 'unique:teams,name,' . $id]
        ] );
        $team = $this->teams->update( $id, [
            'name' => $request->name,
            'slug' => Str::slug( $request->name )
        ] );
        return new TeamResource( $team );
    }

    public function findById($id)
    {
        $team = $this->teams->find( $id );
        return new TeamResource( $team );
    }

    public function fetchUserTeams()
    {
        $teams = $this->teams->fetchUserTeams();
        return TeamResource::collection( $teams );
    }

    public function findBySlug($slug)
    {

    }

    public function destroy($id)
    {
        $team = $this->teams->find( $id );
        $this->authorize( 'delete', $team );

        $this->teams->delete( $id );
        return response()->json( ['status' => true, 'message' => 'Record deleted'], 200 );
    }

    public function removeFromTeam($team_id, $user_id)
    {
        $team = $this->teams->find( $team_id );
        $user = $this->users->find( $user_id );
        // check that the user is not the owner
        if ($user->isOwnerOfTeam( $team )) {
            return $this->fireResponse( false, 'You are the team owner' );
        }
        if (!auth()->user()->isOwnerOfTeam( $team ) && auth()->id() !== $user->id) {
            return $this->fireResponse( false, 'You can not do this' );
        }
        $this->invitations->removeUserFromTeam( $team, $user_id );
        return $this->fireResponse( true, 'Success' );

    }
}
