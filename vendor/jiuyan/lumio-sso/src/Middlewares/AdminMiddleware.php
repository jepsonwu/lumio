<?php
/**
 * Created by IntelliJ IDEA.
 * User: topone4tvs
 * Date: 2017/3/13
 * Time: 17:43
 */

namespace Jiuyan\LumioSSO\Middlewares;

use Closure;
use Auth;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard()->user()) {
            $redirectUrl = app(AdminAuthService::class)->getLoginUrl($request->getUri());
            return redirect($redirectUrl);
        }

        return $next($request);
    }
}