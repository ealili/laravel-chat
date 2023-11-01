<?php

namespace App\Http\Controllers;

use App\Events\NewChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function rooms(Request $request)
    {
        return ChatRoom::all();
    }

    public function messages(Request $request, $roomId)
    {
        return ChatMessage::where('chat_room_id', $roomId)
            ->with('user')
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function newMessage(Request $request, $roomId)
    {
        $newChatMessage = new ChatMessage();
        $newChatMessage->user_id = Auth::id();
//        $user = User::find(Auth::id());
        $newChatMessage->chat_room_id = $roomId;
        $newChatMessage->message = $request->message;

        $newChatMessage->save();

//        event(new NewChatMessage($newChatMessage, Auth::user()));

        broadcast(new NewChatMessage($newChatMessage, Auth::user()))->toOthers();

        return $newChatMessage;
    }

}
