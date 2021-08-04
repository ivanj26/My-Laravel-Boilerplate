<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helper\GeneralHelper;

class ConvertRequestToCamelCase
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
        $payload = $request->all();
        $camelRequest = [];
        $camelRequest = GeneralHelper::toCamelCase($payload);
        $request->replace($camelRequest);

        return $next($request);
    }
}
