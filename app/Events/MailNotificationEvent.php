<?php

 namespace App\Events;

 use App\Notifications\Notification;
 use Illuminate\Broadcasting\InteractsWithSockets;
 use Illuminate\Broadcasting\PrivateChannel;
 use Illuminate\Foundation\Events\Dispatchable;
 use Illuminate\Queue\SerializesModels;

 class MailNotificationEvent
 {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * notification instance
    * 
    * 
    * @var Notification
    */
    public $notification;

    /**
     * email to send to
    * 
    * @var string
    */
    public $to;

    /**
     * cc email
    * 
    * @var string|array
    */
    public $cc;

    /**
     * Array of file attachments
    * 
    * @var array
    */
    public $attachments;

    /**
     * Create a new event instance.
    *
    * @return void
    */
    public function __construct(Notification $notification, string $to = null, array|string $cc = [], array $attachments = [])
    {
        $this->notification = $notification;
        $this->to = $to;
        $this->cc = $cc;
        $this->attachments = $attachments;
    }

    /**
     * Get the channels the event should broadcast on.
    *
    * @return \Illuminate\Broadcasting\Channel|array
    */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}