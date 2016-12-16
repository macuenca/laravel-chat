<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageSent;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function listen()
    {
        return view('listen');
    }

    /**
     * Start a conversation with a representative
     */
    public function start()
    {
        return view('start');
    }

    /**
     * Renders a customer-initiaded conversation view
     *
     * @param int $conversationId
     * @param int $representativeId
     * @return \Illuminate\Http\Response
     */
    public function conversation($conversationId, $representativeId)
    {
        $user = Auth::user();
        $chatMessage = $user->sentMessages()->create([
            'sender_id' => $user->id,
            'receiver_id' => $representativeId,
            'conversation_id' => $conversationId,
            'message' => $user->name . ' joined the conversation!',
        ]);

        // Trigger the event to be broadcast
        broadcast(new ChatMessageSent($chatMessage))->toOthers();

        return view('conversation', ['conversationId' => $conversationId]);
    }
}
