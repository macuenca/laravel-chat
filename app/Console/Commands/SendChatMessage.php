<?php

namespace App\Console\Commands;

use App\ChatMessage;
use App\Events\ChatMessageSent;
use Illuminate\Console\Command;

class SendChatMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:message {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Broadcast chat message';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $message = ChatMessage::create([
            'sender_id' => 1,
            'receiver_id' => 1,
            'conversation_id' => 1,
            'message' => $this->argument('message'),
        ]);

        event(new ChatMessageSent($message));
    }
}
