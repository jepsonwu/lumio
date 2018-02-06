<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/16
 * Time: 15:24
 */
namespace Jiuyan\Common\Component\InFramework\Traits;

use Jiuyan\Common\Component\InFramework\Components\RequestParamsComponent;


/**
 * 用来管理http请求中的一些公共参数
 * 调用某些thrift请求的时候，需要传入这些公共参数
 * Trait InThriftCommonServiceTrait
 * @package Jiuyan\Common\Component\InFramework\Traits
 */
trait InThriftCommonServiceTrait
{
    protected static $_commonParams = [];

    public function initCommonParams($commonParams)
    {
        if (!self::$_commonParams) {
            self::$_commonParams = $commonParams;
        }
    }

    public function getAllRequestCommonParams()
    {
        $this->addRequestCommonParam('ip', RequestParamsComponent::ip());
        $this->addRequestCommonParam('port', RequestParamsComponent::port());
        return json_encode(self::$_commonParams);
    }

    public function appendRequestCommonParam($key, $val)
    {
        self::$_commonParams[$key] = $val;
    }

    public function getRequestCommonParam($paramKey)
    {
        return isset(self::$_commonParams[$paramKey]) ? self::$_commonParams[$paramKey] : false;
    }
}