<?php

namespace App\Http\Controllers\Api\Document;

use App\Events\UploadDocumentEvent;
use App\Models\Document;
use App\Http\Controllers\Api\BaseController;
use App\Http\Modules\DocumentModule;

use App\Http\Requests\Document\BulkStoreDocumentRequest;
use App\Http\Requests\Document\StoreDocumentRequest;

use App\Helper\ServiceCallerHelper;

use Carbon\Carbon;
use Exception;
use GuzzleHttp\Promise\EachPromise;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
class DocumentController extends BaseController
{
    /**
     * The service name.
     * 
     * @var String
     */
    protected $name = 'document service';

    /**
     * Document module.
     * 
     * @var DocumentModule
     */
    private $module;

    /**
     * constructor of CategoryController.
     *
     */
    public function __construct()
    {
        $this->module = new DocumentModule();
    }

    /**
     * store a new documents to App filesystem.
     * 
     * @param StoreDocumentRequest $request
     * @return \Illuminate\Http\Response $response
     */
    public function store(StoreDocumentRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();
        $table = data_get($validated, 'table');
        $tableId = data_get($validated, 'table_id');
        $content = data_get($validated, 'content');
        $filename = data_get($validated, 'filename');
        $filename = data_get(Str::of($filename)->explode('.'), '0');
        // - get file mimetype
        $content = base64_decode($content, true);
        $f = finfo_open();
        $mimeType = finfo_buffer($f, $content, FILEINFO_MIME_TYPE);
        finfo_close($f);
        // - get file ext
        $mimes = new \Mimey\MimeTypes; 
        $extension = $mimes->getExtension($mimeType);
        if (!$extension) {
            $prefixExts = ['text', 'application', 'audio', 'video'];
            foreach ($prefixExts as $pExt) {
                $extension = $mimes->getExtension("{$pExt}/" . data_get(Str::of($mimeType)->explode('/'), '1'));
                if ($extension) break;
            }
        }
        // - construct filename
        $filename = $filename. '-' . Str::random(10) . '-' . time() . ".{$extension}";
        // - prepare upload file to filesystem
        $type = 'documents';
        if(strstr($mimeType, "video/")){
            $type = 'videos';
        } else if(strstr($mimeType, "image/")){
            $type = 'images';
        }
        $date = Carbon::now()->isoFormat('DDMMYYYY');
        $destination = "/uploads/{$type}/{$date}/" . $filename;
        $url = "/api/v1/documents/{$filename}";
        $url = env('APP_ENV') === 'local'
            ? env('APP_URL').':'.env('APP_PORT'). $url
            : env('APP_URL'). $url;

        try {
            $doc = new Document();
            $doc->url = $url;
            $doc->filename = $filename;
            $doc->table = $table;
            $doc->table_id = $tableId;
            $doc->mime_type = $mimeType;
            $doc->type = $type;
            $doc->uploader_id = $user->id;

            // send event
            event(new UploadDocumentEvent($doc, $destination, $content));
        } catch (\Exception $e) {
            $this->throwError(500, $e->getMessage());
        }

        return $this->sendResponse([
            'url' => $url,
            'filename' => $filename
        ], 201);
    }

    /**
     * bulk upload files to File system
     * 
     * @param BulkStoreDocumentRequest $request
     * @return \Illuminate\Http\Response $response
     */
    public function bulkStore(BulkStoreDocumentRequest $request)
    {
        $validated = $request->validated();
        $files = data_get($validated, 'files');

        try {
            $results = [];
            $promises = (function() use($files) {
                foreach ($files as $file) {
                    yield ServiceCallerHelper::call('POST', '/api/v1/documents', $file);
                }
            }) ();
            $eachPromises = new EachPromise($promises, [
                'concurrency' => 10,
                'fulfilled' => function ($response) use(&$results) {
                    $results[] = $response;
                },
                'rejected' => function (\Exception $err) {
                    echo $err->getMessage();
                }
            ]);
            $eachPromises->promise()->wait();

            return $this->sendResponse($results, 201);
        } catch (\Exception $e) {
            $this->throwError(400, $e->getMessage());
        }
    }

    /**
      * Get list of document types
      * 
      * @return \Illuminate\Http\Response $response
      */
    public function documentableTypes()
    {
        $results = [];
        $files = File::allFiles(app_path() . '/Models');
        foreach ($files as $file) {
            $name = $file->getFilenameWithoutExtension();
            $modelPath = 'App\Models\\'. $name;

            try {
                $model = app($modelPath);
                if ($model->hasDocumentable()) {
                    $results[Str::lower($name)] = $modelPath;
                }
            } catch (\Exception $e) {
                // ignore error
                // if its not instantiable
                continue;
            }
        }

        return $this->sendResponse($results);
    }

    /**
     * Get file by filename
     * 
     * @param string $filename
     * @return \Illuminate\Http\Response $response
     */
    public function getByFilename(string $filename)
    {
        try {
            $document = $this
                ->module
                ->findOneBy('filename', "{$filename}");
            $uploadDate = Carbon::createFromFormat(
                    'Y-m-d H:i:s', $document->created_at
                )
                ->isoFormat('DDMMYYYY');
            $path = storage_path('app/public' . "/uploads/{$document->type}/{$uploadDate}/{$filename}");
            if (!File::exists($path)) {
                throw new Exception('file is not found!', 404);
            }

            $file = File::get($path);
            $type = File::mimeType($path);
            $response = Response::make($file, 200);
            $response->header("Content-Type", $type);

            return $response;
        } catch (\Exception $e) {
            $this->throwError($e->getCode(), $e->getMessage());
        }
    }
}
