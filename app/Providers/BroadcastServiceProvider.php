<?php

namespace App\Providers;

use App\ChatMessage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes();

        /*
         * Authenticate the user's personal channel...
         */
        Broadcast::channel('conversation.*', function ($user, $conversationId) {
            return $user->id == ChatMessage::where('conversation_id', $conversationId)->first()->sender_id ||
                   $user->id == ChatMessage::where('conversation_id', $conversationId)->first()->receiver_id;
        });
    }
}
