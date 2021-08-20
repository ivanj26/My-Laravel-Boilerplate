<?php

namespace App\Events;

use App\Models\Document;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UploadDocumentEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Document model
     * 
     * @var Document
     */
    public Document $document;

    /**
     * file upload destination path
     * 
     * @var string
     */
    public string $destination;

    /**
     * file in base64 string
     * 
     * 
     * @var string|false
     */
    public $content;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Document $document, string $destination, string|false $content)
    {
        $this->document = $document;
        $this->destination = $destination;
        $this->content = $content;
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
