<?php

namespace App\Http\Controllers;

use App\ChatMessage;
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::guard('api')->user();
        return 'Hello World!';
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
            'receiver_id' => 1,
            'conversation_id' => $request->conversation_id,
            'message' => $user->name . ': ' . $request->message,
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
        // Make sure the logged-in owns the conversation
        $user = Auth::guard('api')->user();
        $conversation = ChatMessage::where(
            [
                'conversation_id' => $id,
                'user_id' => $user->id
            ]
        )->firstOrFail();
        $conversation->delete();

        // Return a no content response
        return Response::json([], 204);
    }
}
