<?php
namespace App\Repositories\Contacts;
interface InvitationInterface
{
    public function addUserToTeam($team,$user_id);
    public function removeUserFromTeam($team,$user_id);
}
