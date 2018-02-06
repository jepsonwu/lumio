<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/11/29
 * Time: 11:35
 */

namespace Jiuyan\Common\Component\InFramework\Middleware;

use Closure;

class ApiSignatureMiddleware
{
    public function handle($request, Closure $next, $guard = null)
    {
        return $next($request);
    }
}