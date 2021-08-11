<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helper\GeneralHelper;

class ConvertResponseToCamelCase
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
        $response = $next($request);
        $content = $response->getContent();
        $contentType = $response->headers->get('content-type');

        try {
            if ($contentType !== 'application/json') return $response;
            $json = json_decode($content, true);
            $data = $json['data'];
            $camelResponse = GeneralHelper::toCamelCase($data);
            $json['data'] = $camelResponse;
            $result = json_encode($json);

            $response->setContent($result);
        } catch (\Exception $e) {
            throw $e;
        }

        return $response;
    }
}
