<?php

namespace Jiuyan\Common\Component\Middlewares;

use Closure;

class LogMiddleware
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        $statusCode = $response->getStatusCode();
        $level = 'info';
        if ($statusCode >= 500) {
            $level = 'error';
        } elseif ($statusCode >= 400) {
            $level = 'warning';
        }
        $route = $request->route();
        if (! isset($route)) {
            return;
        }
        if (isset($route[1]['controller'])) {
            $actionName = $route[1]['controller'];
        } else {
            $action = explode('@', $route[1]['uses']);
            $actionName = array_reverse(explode('\\', $action[0]))[0] .'@'. $action[1];
        }

        $actionTime = intval(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']);

        $message =
            $actionName
            .' '.json_encode($request->all(), true)
            .' '.$actionTime
            .' '.$response->getStatusCode();

        \Log::$level($message);
    }
}
