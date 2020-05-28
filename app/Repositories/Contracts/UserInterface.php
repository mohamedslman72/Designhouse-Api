<?php
namespace App\Repositories\Contacts;
use Illuminate\Http\Request;

interface UserInterface
{
    public function findEmail($email);
    public function search(Request $request);
}
