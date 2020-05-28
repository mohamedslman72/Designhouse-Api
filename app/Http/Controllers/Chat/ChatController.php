<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;
use App\Repositories\Contacts\ChatInterface;
use App\Repositories\Contacts\MessageInterface;
use App\Repositories\Eloquent\Criteria\WithTrashed;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    protected $chats;
    protected $messages;

    public function __construct(ChatInterface $chats, MessageInterface $messages)
    {
        $this->chats = $chats;
        $this->messages = $messages;
    }

    // Send message to user
    public function sendMessage(Request $request)
    {
        $this->validate( $request, [
            'recipient' => 'required',
            'body' => 'required',
        ] );
        $recipient = $request->recipient;
        $user = auth()->user();
        $body = $request->body;
        // check if there is an existing chant
        // between the auth user and the recipient
        $chat = $user->getChatWithUser( $recipient );
        if (!$chat) {
            $chat = $this->chats->create( [] );
            $this->chats->createParticipant( $chat->id, [$user->id, $recipient] );
        }
        // add message to the chat

        $meesage = $this->messages->create( [
            'user_id' => $user->id,
            'chat_id' => $chat->id,
            'body' => $body,
            'last_read' => null
        ] );
        return new MessageResource( $meesage );
    }

    // Get chats for user
    public function getUserChats()
    {
        $chats = $this->chats->getUserChats();
        return ChatResource::collection( $chats );
    }

    // Get chats for user
    public function getChatMessages($id)
    {
        $messages = $this->messages->withCriteria([
            new WithTrashed()
        ])->findWhere('chat_id',$id);
        return $this->fireResponse(true,'Successful',MessageResource::collection($messages));
    }

    // mark chat as read
    public function markAsRead($id)
    {
        $chat = $this->chats->find($id);
        $chat->markAsReadForUser(auth()->id());
        return $this->fireResponse(true,'Successful');
    }

    // destroy message
    public function destroyMessage($id)
    {
        $message = $this->messages->find($id);
        $this->authorize('delete',$message);
        $message->delete();
        return $this->fireResponse(true,'Deleted Successfully');

    }
}
