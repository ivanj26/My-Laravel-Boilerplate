<?php

 namespace App\Listeners;

 use App\Events\MailNotificationEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Notification;

 class MailNotificationListener implements ShouldQueue
 {
    /**
     * Name of the connection the job should be sent to.
     *
     * @var string|null
     */
    public $connection = 'database';

    /**
     * Name of the connection the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'listener';

    /**
     * Delay before the job be consumed.
     *
     * @var string|null
     */
    public $delay = 5;

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
    public function handle(MailNotificationEvent $event)
    {
        try {
            $notification = $event->notification;
            $toEmail = $event->to;
            $ccEmails = $event->cc;
            $attachments = $event->attachments;

            // - set cc
            $notification->setCc($ccEmails);

            // - set attachments here
            foreach ($attachments as $attachment) {
                $content = data_get($attachment, 'content');
                $fileName = data_get($attachment, 'filename');

                // - get file mimetype
                $decoded = base64_decode($content, true);
                $f = finfo_open();
                $mimeType = finfo_buffer($f, $decoded, FILEINFO_MIME_TYPE);
                finfo_close($f);

                $notification->addAttachment([
                    'base64' => $content,
                    'filename' => $fileName,
                    'mimetype' => $mimeType
                ]);
            }

            // - send notification on-demand
            Notification::route('mail', $toEmail)
                ->notify($notification);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        // Set to default locale
        App::setLocale('en');
    }
}
