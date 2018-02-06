<?php
namespace Jiuyan\Request\Tool;

/**
 * Created by PhpStorm.
 * User: xinghuo
 * Date: 2017/8/9
 * Time: 下午4:07
 */

use Closure;

class SignMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $signFlag = env('REQUEST_SIGN_FLAG', '_sign');
        $expire = env('REQUEST_SIGN_EXPIRE', 1800);
        $prams = $request->all();
        $sign = $request->input($signFlag);
        $str = app(SignInterface::class)->generateStringForSign($prams, false);
        $sign = app(SignInterface::class)->checkSign($str, $sign, $expire, 1);
        if (!$sign) {
            throw new SignException("", app(SignInterface::class)->getLastError());
        }
        return $next($request);
    }
}
