<?php

namespace App\Helper;

use Illuminate\Http\Request;

class ServiceCallerHelper {
    /**
     * Call internal service using call method.
     * 
     * @param string $method GET|POST|PUT|DELETE|PATCH.
     * @param string $endpoint
     * @param array $params
     * @return mixed $response
     */
    public static function call(string $method, string $endpoint, array $params = [])
    {
        $request = Request::create($endpoint, $method, $params);
        $response = app()->handle($request);
        $response = json_decode($response->getContent());
        return $response->data;
    }
}