<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/11/20
 * Time: 16:11
 */

namespace Tests;

use Jiuyan\Common\Component\InFramework\Components\CommonToolsComponent;

class ApiTestCase extends TestCase
{
    protected $_commonParams = [];

    protected function _setCommonParam($key, $val)
    {
        $this->_commonParams[$key] = $val;
    }

    protected function _appendCommonParams(&$params)
    {
        $commonParams = [
            'sign' => '2.ye73de5a837caaf8a185c4d443735cf531512702925',
            '_ch' => 'itugo',
            '_emu' => 'false',
            '_g' => 'f',
            '_gps' => '120.124402,30.272881',
            '_imei' => '865790020391918',
            '_n' => 'WIFI',
            '_osv' => '22',
            '_pf' => 'YQ601',
            '_res' => '1080',
            '_s' => 'android',
            '_udid' => 'ariesGCMea140a62af0e9245193f0409ea10c979',
            '_uuid' => '59f083e553254b50',
            '_v' => '3.2.50',
            '_wm' => '58:b6:33:0d:d5:e8',
            '_wn' => 'jywifi',
            'tdid' => 'B05BC1A4-3F26-4B09-8F8A-BB0856D1285E',
            '_idfa' => 'B05BC1A4-3F26-4B09-8F8A-BB0856D1285E',
        ];
        $commonParams = array_merge($commonParams, $this->_commonParams);
        $params = array_merge($commonParams, $params);
    }

    protected function _getApiResponse($apiRouteName, $params, $requestMethod)
    {
        $apiHost = $this->_getApiUrlByRouteName($apiRouteName);
        $this->_appendCommonParams($params);
        $this->json($requestMethod, $apiHost, $params);
        $responseStr = $this->response->getContent();
        $response = json_decode($responseStr, true);
        if (!isset($response['succ']) || (!$response['succ'] && !$response['code'])) {
            echo "\n" . 'api response route:' . $apiRouteName . ' error:' . $response['code'] . ' msg:' . $response['msg'] . PHP_EOL;
            echo 'params:' . json_encode($params) . PHP_EOL;
            return true;
        }
        $this->assertTrue(is_array($response['data']));
        return $response;
    }

    protected function _assertApi($apiRouteName, $params, $requestMethod, $assertResult)
    {
        $assertResult = array_merge(
            [
                'succ' => 'boolean',
                'code' => 'string',
                'msg' => 'string',
                'time' => 'string',
                'data' => [],
            ],
            $assertResult
        );
        $response = $this->_getApiResponse($apiRouteName, $params, $requestMethod);
        if (true !== $response) {
            $this->_testJsonResult($assertResult, $response);
            //$this->seeJsonStructure($assertResult);
        }
        /**
         * api响应结果，直接可以获取
         * TODO 后续可以加入对于字段类型的断言
         */
        $response = $response['data'] ?? [];
        return $response;
    }

    protected function _testDataType()
    {

    }

    protected function _getApiUrlByRouteName($routeName)
    {
        $fullUrl = CommonToolsComponent::getRouteUrl($routeName);
        $pathUrl = preg_replace('/(http|https):\/\/[^\/]*(:\d+)?/', '', $fullUrl);
        return $pathUrl;
    }
}