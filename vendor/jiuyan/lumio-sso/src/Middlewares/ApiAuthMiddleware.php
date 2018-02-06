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
use Exception;

class ApiAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $currentUser = Auth::guard()->user();
        if (!$currentUser) {
            throw new Exception('当前账号已退出', '1000001');
        }

        if (app()->environment() != 'local') {
            $source = $request->input('_s');
            $authVal = $request->cookie('_aries') ?: $request->input('_at');
            $checkLogin = false;
            if (!$authVal && $source == 'android') {
                $checkLogin = true;
            } elseif ($authVal === $currentUser->_auth) {
                $checkLogin = true;
            }
            if (!$checkLogin) {
                throw new Exception('账号已退出或在别处登录', '1000002');
            }
        }
        return $next($request);
    }
}
