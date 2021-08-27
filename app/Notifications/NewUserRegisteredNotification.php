<?php

 namespace App\Notifications\User;

 use App\Helper\GeneralHelper;
 use App\Notifications\BaseNotification;
 use Illuminate\Notifications\Messages\MailMessage;

 class NewUserRegisteredNotification extends BaseNotification
 {
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

        return (new MailMessage)
                    ->subject($subject)
                    ->view($htmlPath, $this->data);
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