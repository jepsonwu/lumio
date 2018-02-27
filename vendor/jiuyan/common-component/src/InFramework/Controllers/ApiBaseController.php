<?php

namespace Jiuyan\Common\Component\InFramework\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Jiuyan\Common\Component\InFramework\Components\RequestParamsComponent;
use Jiuyan\Common\Component\InFramework\Exceptions\ApiExceptions;
use Jiuyan\Tools\Business\JsonTool;
use Laravel\Lumen\Routing\Controller;
use Prettus\Repository\Contracts\Transformable;

class ApiBaseController extends Controller
{
    /**
     * 用以提取请求中的参数
     * @var RequestParamsComponent
     */
    public $requestParams = null;

    public function initRequestParams($regularParams = [])
    {
        $this->requestParams = new RequestParamsComponent($regularParams);
    }

    protected $_cookies = [];

    public function addCookie($name, $val, $expires, $domain = '.in66.com')
    {
        $this->_cookies[] = [
            'name' => $name,
            'val' => $val,
            'expires' => $expires,
            'domain' => $domain
        ];
    }

    public function getRouteUrl($routeName, $apiVersion = 'v1')
    {
        return app('Dingo\Api\Routing\UrlGenerator')->version($apiVersion)->route($routeName);
    }

    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $commonParams = [];
        if ($rules) {
            $validator = $this->getValidationFactory()->make($request->all(), $rules, $messages, $customAttributes);
            if ($validator->fails()) {
                $this->throwValidationException($request, $validator);
            }
            $commonParams = $validator->attributes();
        }
        $this->initRequestParams($commonParams);
    }

    public function success($data)
    {
        return $this->result(true, $data);
    }

    public function result($status = true, $data = [], $codeTpl = '')
    {
        $response = new Response();
        $responseRes = explode('|', $codeTpl);
        $data = $this->formatResultData($data);

        $response->setContent(
            JsonTool::encode([
                'succ' => $status,
                'data' => $data,
                'code' => $responseRes[0] ?: 0,
                'msg' => $responseRes[1] ?? '',
                'time' => time()
            ])
        );
        $response->header('Content-Type', 'application/json');
        if ($this->_cookies) {
            foreach ($this->_cookies as $item) {
                $response->withCookie(
                    cookie($item['name'], $item['val'], $item['expires'], null, $item['domain'], false, false)
                );
            }
        }
        return $response;
    }

    protected function formatResultData($data)
    {
        $extend = [];
        $isPaginator = false;
        if ($data instanceof LengthAwarePaginator) {
            $isPaginator = true;
            $extend = $data->toArray();
            $data = $data->items();
        }

        if ($data instanceof Collection) {
            $data = $data->each(function ($item) {
                return $this->formatDataItem($item);
            })->toArray();
        } elseif (is_array($data)) {
            $data = array_map(function ($item) {
                return $this->formatDataItem($item);
            }, $data);
        } else {
            $data = $this->formatDataItem($data);
        }

        if ($isPaginator) {
            $extend['data'] = $data;
            $data = $extend;
        }

        return $data;
    }

    protected function formatDataItem($item)
    {
        if ($item instanceof Transformable) {
            $item = $item->transform();
        } elseif (is_object($item) && method_exists($item, 'toArray')) {
            $item = $item->toArray();
        }

        return $item;
    }

    /**
     * @param $errorTpl = '100001|系统错误'
     * @throws ApiExceptions
     */
    public function error($errorTpl)
    {
        $errorInfo = explode('|', $errorTpl);
        $errCode = $errorInfo[0] ?? 100001;
        $errMsg = $errorInfo[1] ?? 'system error';
        throw new ApiExceptions($errMsg, $errCode);
    }
}
