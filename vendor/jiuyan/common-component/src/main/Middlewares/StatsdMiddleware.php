<?php

namespace Jiuyan\Common\Component\Middlewares;

use Closure;

class StatsdMiddleware
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        if (\App::environment('testing')) {
            return;
        }

        $route = $request->route();

        if (! isset($route)) {
            return;
        }

        if (isset($route[1]['controller'])) {
            $actionName = $route[1]['controller'];
        } else {
            $action = explode('@', $route[1]['uses']);
            $actionName = array_reverse(explode('\\', $action[0]))[0] . $action[1];
        }

        $actionTime = intval(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']);
        $statusCode = $response->getStatusCode();
        if ($statusCode >= 500) {
            \Statsd::timing("restapi.error." . $actionName, $actionTime);
        } else {
            \Statsd::timing("restapi.action.".$actionName, $actionTime);
        }
    }
}
