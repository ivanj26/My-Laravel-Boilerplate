<?php

namespace App\Listeners;

use App\Events\UploadDocumentEvent;
use App\Http\Modules\DocumentModule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UploadDocumentListener
{
    /**
     * Document module.
     * 
     * @var DocumentModule
     */
    private $module;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->module = new DocumentModule();
    }

    /**
     * Handle the event.
     *
     * @param  UploadDocumentEvent  $event
     * @return void
     */
    public function handle(UploadDocumentEvent $event)
    {
        try {
            $document = $event->document;
            $destination = $event->destination;
            $content = $event->content;
            $table = $document->table;

            // - check model if the table name is exists
            if (!empty($table)) {
                $tableName = data_get(
                    Str::of($table)->explode('\\'),
                    '2'
                );
                $moduleName = 'App\Http\Modules\\'. $tableName . 'Module';
                $tableId = $document->table_id;

                $modelModule = app($moduleName);
                $modelModule->findOneBy('id', $tableId);
            }

            $success = Storage::disk('public')
                    ->put($destination, $content);
            if ($success) {
                $filesize = filesize(storage_path('app/public' . $destination));
                $payload = [
                    'url' => $document->url,
                    'filename' => $document->filename,
                    'size' => $filesize,
                    'mime_type' => $document->mime_type,
                    'table' => $table ?? NULL,
                    'table_id' => $tableId ?? 0,
                    'type' => $document->type,
                    'uploader_id' => $document->uploader_id,
                ];
                // - start trx
                DB::beginTransaction();
                // store new document into database
                $this->module
                    ->create($payload);
                // - commit
                DB::commit();                
            }
        } catch (\Exception $e) {
            DB::rollback();

            // @TODO: change to logger
            echo $e->getMessage();
        }
    }
}
