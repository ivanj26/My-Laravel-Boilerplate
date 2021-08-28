<?php

namespace App\Providers;

use App\Events\MailNotificationEvent;
use App\Events\UploadDocumentEvent;
use App\Listeners\MailNotificationListener;
use App\Listeners\UploadDocumentListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UploadDocumentEvent::class => [
            UploadDocumentListener::class
        ],

        MailNotificationEvent::class => [
            MailNotificationListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
