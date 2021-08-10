<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if ($user->tokenCan('admin')) {
            return $next($request);
        }

        $code = 403;
        $response = [
            'statusCode' => $code,
            'data' => null,
            'message' => 'forbidden access'
        ];
        throw new HttpResponseException(
            response()->json($response, $code)
        );
    }
}
