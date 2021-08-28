<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;

class BaseController extends Controller {
    protected $name = 'default';

    /**
     * Begin the transaction.
     * 
     * @return void
     */
    protected function startTrx()
    {
        DB::beginTransaction();
    }

    /**
     * Commit the transaction.
     * 
     * @return void
     */
    protected function commitTrx()
    {
        DB::commit();
    }

     /**
      * Rollback the transaction.
      *
      */
    protected function rollbackTrx()
    {
        DB::rollBack();
    }

    /**
     * send success response method
     * @param array|null $data
     * @param int $code
     * @param string $message 
     * @return \Illuminate\Http\Response $response
     */
    public function sendResponse($data = null, $code = JsonResponse::HTTP_OK, $message = '')
    {
        $trace = debug_backtrace()[2];
        $actionName = $trace['args'][0];
        $format = 'success call %s action in %s';
        $message = isEmpty($message) ? sprintf($format, $actionName, $this->name) : $message;
        $response = [
            'statusCode' => $code,
            'data' => $data,
            'message' => $message
        ];

        if ($data instanceof LengthAwarePaginator) {
            $response['data'] = $data->items();
            $response['meta'] = [
                'count' => count($data->items()),
                'total' => $data->total(),
                'perPage' => (int) $data->perPage(),
                'page' => $data->currentPage(),
                'totalPage' => $data->lastPage(),
            ];
        }

        return response()->json($response, $code);
     }

     /**
      * throw an error response.
      *
      * @param string $code
      * @param string $message
      * @return \Illuminate\Http\Response $response
      */
    public function throwError($code = 404, $message = '')
    {
        $response = [
            'statusCode' => $code,
            'data' => null,
            'message' => $message
        ];

        throw new HttpResponseException(
            response()->json($response, $code)
        );
    }
}