<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Exceptions\HttpResponseException;
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
     * destructor of BaseController.
     * 
     */

    /**
     * send success response method
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($data = null, $code = 200, $message = '')
    {
        $format = 'success call %s';
        $message = isEmpty($message) ? sprintf($format, $this->name) : $message;
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

        return response()->json($response, 200);
     }

     /**
      * throw an error
      * @return \Illuminate\Http\Response
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