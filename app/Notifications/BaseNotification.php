<?php

 namespace App\Notifications;

 use Illuminate\Bus\Queueable;
 use Illuminate\Contracts\Queue\ShouldQueue;
 use Illuminate\Notifications\Notification;

 abstract class BaseNotification extends Notification implements ShouldQueue
 {
    use Queueable;

    /**
     * Data information
    * @var array data
    */
    protected $data = null;

    /**
     * Notif template information
    * @var \App\Models\NotificationTemplate
    */
    protected $template = null;

    /**
     * Create a new notification instance.
    *
    * @param array $data
    * @param \App\Models\NotificationTemplate $template
    * @return void
    */
    public function __construct($data, $template)
    {
        $this->data = $data;
        $this->template = $template;
    }
 } 