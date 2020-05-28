<?php
namespace App\Repositories\Contacts;
use Illuminate\Http\Request;

interface DesignInterface
{
    public function applyTags($id,array $data);
//    public function allLive();
    public function addComment($design_id,array $data);
    public function like($id);
    public function isLikedByUser($id);
    public function search(Request $request);
}
