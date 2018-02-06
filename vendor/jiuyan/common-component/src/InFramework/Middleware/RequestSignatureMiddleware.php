<?php
/**
 * Created by IntelliJ IDEA.
 * User: topone4tvs
 * Date: 2017/3/22
 * Time: 16:30
 */

namespace Jiuyan\Common\Component\InFramework\Middleware;

use Illuminate\Http\Request;
use Jiuyan\Common\Component\InFramework\Components\RequestParamsComponent;
use Jiuyan\Common\Component\InFramework\Exceptions\ServiceException;
use Closure;
use Log;

class RequestSignatureMiddleware
{
    /**
     * @var RequestParamsComponent
     */
    public $requestParamsComponent;

    public function __construct(RequestParamsComponent $requestParamsComponent)
    {
        $this->requestParamsComponent = $requestParamsComponent;
    }

    public function handle(Request $request, Closure $next)
    {
        if (app()->environment() == 'product') {
            $sign = trim($request->offsetGet('sign'));
            if ($sign == '' || strlen($sign) != 45) {
                Log::error('signature failed for sign-str invalid sign:' . $sign);
                throw new ServiceException('signature failed for sign-str invalid');
            }
            $params = $request->all();
            if (isset($params['inxhprof'])) unset($params['inxhprof']);
            if (isset($params['indebug'])) unset($params['indebug']);
            $paramsStr = $this->_formatParamsStr($params);
            if (!jysign_check($paramsStr, $sign, 1800)) {
                Log::error('signature failed for sign check error sign:' . $sign . ' params:' . $paramsStr);
                throw new ServiceException('signature failed for sign check');
            }
        }
        return $next($request);
    }

    private function _formatParamsStr($params)
    {
        $finalParams = array();
        foreach ($params as $key => $val) {
            if ($key == 'sign' || $val === '') continue;
            $finalParams[$key] = $params[$key];
        }
        ksort($finalParams);
        reset($finalParams);
        $paramsStr = http_build_query($finalParams);
        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $paramsStr = stripslashes($paramsStr);
        }
        return $paramsStr;
    }
}