<?php

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use App\Mail\SendIvitationToJoinTeam;
use App\Models\Team;
use App\Repositories\Contacts\InvitationInterface;
use App\Repositories\Contacts\TeamInterface;
use App\Repositories\Contacts\UserInterface;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;

class InvitationController extends Controller
{
    protected $invitations;
    protected $teams;
    protected $users;

    public function __construct(InvitationInterface $invitations, TeamInterface $teams, UserInterface $users)
    {
        $this->invitations = $invitations;
        $this->teams = $teams;
        $this->users = $users;
    }

    public function invite(Request $request, $teamId)
    {
        //get the team
        $team = $this->teams->find( $teamId );
        $this->validate( $request, [
            'email' => ['required', 'email']
        ] );
        $user = auth()->user();
        // check if the user owns the team
        if (!$user->isOwnerOfTeam( $team )) {
            return response()->json( ['status' => false, 'message' => __( 'You Have no rights to invite' )], 401 );
        }

        // check if the email has pending invitation
        if ($team->hsaPendingInvite( $request->email )) {
            return response()
                ->json( ['status' => false,
                    'message' => __( 'Email already has a pending invitation' )], 401 );
        }
        // get the recipient by email
        $recipient = $this->users->findEmail($request->email);

        // if the recipient does not exist, send invitation to join the team
        if (! $recipient){
            $this->createInvitation(false,$team,$request->email);
            return response()->json(['status'=>true,
                'message'=>__('Invitation sent to the user')],200);
        }

        // check if the team already sent to user
        if ($team->hasUser($recipient)){
            return response()->json(['status'=>false,
                'message'=>__('This user seems to be a team member already')],422);
        }

        // send the invitation to the user
        $this->createInvitation(true,$team,$request->email);
        return response()->json(['status'=>true,
            'message'=>__('Invitation sent to the user')],200);


    }

    public function resend($id)
    {
        $invitation = $this->invitations->find($id);
        // check if the user owns the team
        $this->authorize('resend',$invitation);
//        if (!auth()->user()->isOwnerOfTeam( $invitation->team )) {
//            return response()->json( ['status' => false, 'message' => __( 'You Have no rights to invite' )], 401 );
//        }
// get the recipient by email
        $recipient = $this->users->findEmail($invitation->recipient_email);
        Mail::to($invitation->recipient_email)
            ->send(new SendIvitationToJoinTeam($invitation,!is_null($recipient)));
        return response()->json(['status'=>true,'message'=>__('Invitation resent')],200);
    }

    public function respond(Request $request, $id)
    {
        $this->validate($request,[
            'token' =>['required'],
            'decision' =>['required']
        ]);
        $token = $request->token;
        $decision = $request->decision;// accept or deny
        $invitation = $this->invitations->find($id);
        // check if the invitation belongs to the user
        $this->authorize('respond',$invitation);
//        if ($invitation->recipient_email != auth()->user()->email){
//            return $this->fireResponse(false,"this is not your invitation");
//        }
        // check if accepted
        if ($decision != 'deny'){
            auth()->user()->teams();
            $this->invitations->addUserToTeam($invitation->team,auth()->id());
        }
        $invitation->delete();
        return $this->fireResponse(true,'Successful');

    }

    public function destroy($id)
    {
        $invitation = $this->invitations->find($id);
        $this->authorize('delete',$invitation);
        $invitation->delete();
        return $this->fireResponse(true,'Deleted');
    }
    protected function createInvitation(bool $user_exists,Team $team,string $email){
        $invitation = $this->invitations->create([
            'team_id' =>$team->id,
            'sender_id' =>auth()->id(),
            'recipient_email'=>$email,
            'token'=> md5(uniqid(microtime()))
        ]);
        Mail::to($email)
            ->send(new SendIvitationToJoinTeam($invitation,$user_exists));
    }
}
