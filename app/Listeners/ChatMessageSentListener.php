<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ChatMessageSentListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {

        //
        $botId = 'bot5335209292:AAGW9F99CKlnjh2yagMliVtBvDGKdoC2OLY';
        $chatId = '-915188125';

        $user_id = $event->user_id;
        $message = $event->message;

        if(isset($message) && isset($message->user_id)){
            $tgMessage = "<b>Новое сообщение в чате:</b>\n\n";
            $tgMessage .= "<b>" . $message->message . "</b>\n\n";
            $tgMessage .= "Отправлено: " . $message->created_at . "\n";
            $tgMessage .= "<a href='" . route('admin.chat.index', ['chat_id' => $user_id]) . "'>Открыть в чате &gt;</a>";
            @file_get_contents('https://api.telegram.org/' . $botId . '/sendMessage?chat_id=' . $chatId . '&parse_mode=HTML&text=' . urlencode($tgMessage));
        }
    }
}
