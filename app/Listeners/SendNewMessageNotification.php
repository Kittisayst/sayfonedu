<?php

namespace App\Listeners;

use App\Events\NewMessageEvent;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendNewMessageNotification implements ShouldQueue
{
    protected $notificationService;

    /**
     * Create the event listener.
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(NewMessageEvent $event): void
    {
        $message = $event->message;
        
        // ສົ່ງການແຈ້ງເຕືອນໃຫ້ຜູ້ຮັບຂໍ້ຄວາມ
        $this->notificationService->send(
            $message->receiver_id,
            'ທ່ານໄດ້ຮັບຂໍ້ຄວາມໃໝ່',
            "ທ່ານໄດ້ຮັບຂໍ້ຄວາມໃໝ່ຈາກ {$message->sender->username}: {$message->subject}",
            'new_message',
            $message->id
        );
    }
}