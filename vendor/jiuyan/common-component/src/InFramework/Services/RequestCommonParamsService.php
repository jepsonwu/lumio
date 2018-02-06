<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/16
 * Time: 15:13
 */

namespace Jiuyan\Common\Component\InFramework\Services;

use Jiuyan\Common\Component\InFramework\Components\RequestParamsComponent;

/**
 * 用来管理http请求中的一些公共参数
 * service层在处理一些业务逻辑的时候，可能会需要用到这些数据；event层也会用到
 * Trait RequestCommonParamsService
 * @package Jiuyan\Common\Component\InFramework\Traits
 */
class RequestCommonParamsService
{
    /**
     * @var RequestCommonParamsService
     */
    protected $_commonParamService;

    protected static $_instance = null;

    protected static $_commonParams = [];

    public function __construct()
    {
        if (!self::$_commonParams) {
            self::$_commonParams = $this->_getCommonParams();
        }
    }

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function _getCommonParams()
    {
        if (app()->runningInConsole()) {
            return [];
        }
        return RequestParamsComponent::getAllCommonParams();
    }

    public function getAllRequestCommonParams()
    {
        return self::$_commonParams;
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