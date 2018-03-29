<?php

namespace Jiuyan\Common\Component\InFramework\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CorsMiddleware
{
    private $headers;

    public function handle(Request $request, Closure $next)
    {
        $origin = $request->server("HTTP_ORIGIN", "");
        $crossAllow = config("cors.allow_origins");

        if (empty($origin) || in_array($origin, $crossAllow)
            || in_array('*', $crossAllow) || strpos($origin, "chrome-extension") !== -1
        ) {
            $this->headers = [
                'Access-Control-Allow-Origin' => empty($origin) ? "*" : $origin,
                'Access-Control-Allow-Headers' => 'x-requested-with, Content-Type',
                'Access-Control-Allow-Methods' => '*',
                'Access-Control-Allow-Credentials' => 'true'
            ];
        }

        if ($request->getMethod() == "OPTIONS") {
            $response = new Response('OK', 200);
            foreach ($this->headers as $key => $value) {
                $response->header($key, $value);
            }
            return $response;
        }

        $response = $next($request);
        foreach ($this->headers as $key => $value)
            $response->header($key, $value);
        return $response;
    }
}