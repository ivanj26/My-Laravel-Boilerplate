<?php

namespace App\Notifications;

use App\Helper\GeneralHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification as BaseNotification;
use Illuminate\Support\Facades\App;

class Notification extends BaseNotification implements ShouldQueue
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
     * Locale information
     * @var \App\Models\NotificationTemplate
     */
    public $locale = null;

    /**
     * cc email
     *
     * @var string|array
     */
    private $cc = [];

    /**
     * attachments
     *
     * @var array
     */
    private $attachments = [];

    /**
     * Create a new notification instance.
     *
     * @param array $data
     * @param \App\Models\NotificationTemplate $template
     * @param string $locale
     * @return void
     */
    public function __construct($data, $template, $locale = 'id')
    {
        $this->data = $data;
        $this->locale = $locale;
        $this->template = $template;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * set cc emails
     *
     * @param array|string $cc;
     * @return void
     */
    public function setCc(array|string $cc)
    {
        $this->cc = $cc;
    }

    /**
     * set cc emails
     *
     * @param array $file
     * @return void
     */
    public function addAttachment($file)
    {
        $this->attachments[] = $file;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $subject = data_get($this->template, 'title');
        $subject = GeneralHelper::replaceAllSymbols($subject, $this->data);
        $htmlPath = data_get($this->template, 'template_path');

        $mailMsg = (new MailMessage)
            ->subject($subject)
            ->cc($this->cc);

        foreach ($this->attachments as $attachment) {
            $mailMsg->attachData(
                base64_decode(data_get($attachment, 'base64')),
                data_get($attachment, 'filename'),
                ['mime' => data_get($attachment, 'mimetype')]
            );
        }

        // - set locale before render html view
        App::setLocale($this->locale);

        return $mailMsg->view($htmlPath, $this->data);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
