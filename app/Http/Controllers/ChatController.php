<?php

namespace App\Http\Controllers;

use App\ChatMessage;
use App\User;
use App\Events\ChatMessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Requests;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

/**
 * Chat endpoint
 *
 * Used to perform all operations on chat messages
 */
class ChatController extends Controller
{
    /**
     * Display a list of conversations for the current user
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Make sure the logged-in user participates in the conversations
        $user = Auth::guard('api')->user();

        return DB::table('chat_messages')
            ->select(DB::raw('conversation_id, COUNT(message) AS messages, MAX(created_at) AS date'))
            ->where(function ($query) use ($user) {
                $query
                    ->where('sender_id', '=', $user->id)
                    ->orWhere('receiver_id', '=', $user->id);
            })
            ->groupBy('conversation_id')
            ->orderBy('date', 'desc')
            ->take(self::DEFAULT_PAGE_SIZE)
            ->get()
            ->toJson();
    }

    /**
     * Add a message to a conversation
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'conversation_id' => 'required',
            'message' => 'required'
        ]);

        $user = Auth::guard('api')->user();
        $chatMessage = $user->sentMessages()->create([
            'sender_id' => $user->id,
            'receiver_id' => $request->receiver_id,
            'conversation_id' => $request->conversation_id,
            'message' => $request->message,
            'sender_name' => User::find($user->id)->name,
            'receiver_name' => User::find($request->receiver_id)->name,
        ]);

        // Trigger the event to be broadcast
        broadcast(new ChatMessageSent($chatMessage))->toOthers();

        // Return the newly created object
        return $chatMessage->toJson();
    }

    /**
     * Display all messages in an existing conversation
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Make sure the logged-in user participates in the conversation
        $user = Auth::guard('api')->user();

        return DB::table('chat_messages')
            ->where('conversation_id', '=', $id)
            ->where(function ($query) use ($user) {
                $query
                    ->where('sender_id', '=', $user->id)
                    ->orWhere('receiver_id', '=', $user->id);
            })
            ->take(self::DEFAULT_PAGE_SIZE)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toJson();
    }

    /**
     * Remove a conversation
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Make sure the logged-in participates the conversation
        $user = Auth::guard('api')->user();
        DB::table('chat_messages')
            ->where('conversation_id', '=', $id)
            ->where(function ($query) use ($user) {
                $query
                    ->where('sender_id', '=', $user->id)
                    ->orWhere('receiver_id', '=', $user->id);
            })
            ->delete();

        // Return a no content response
        return Response::json([], 204);
    }
}
