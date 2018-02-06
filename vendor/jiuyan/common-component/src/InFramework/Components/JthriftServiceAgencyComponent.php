<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/16
 * Time: 21:48
 */

namespace Jiuyan\Common\Component\InFramework\Components;

use Jiuyan\Common\Component\InFramework\Exceptions\ThriftResponseException;
use Jiuyan\Cuckoo\ThriftClient\Attacher;
use Log;

class JthriftServiceAgencyComponent
{
    const NOT_SET_SERVER_NAME = -1;

    protected $_thriftDelivery = null;

    protected $_requestInfo = null;

    protected $_retry = 0;

    protected $_call = '';

    protected $_serviceName = null;
    protected $_functionName = '';
    protected $_args = null;

    protected $_isExceptionNeedThrow = false;

    /**
     * @var callable
     */
    protected $_formatResultCallback = null;

    /**
     * 调用自定义格式化方法时，默认只执行一次，
     * 因为一般情况下，默认的格式化方法已经可以满足了，需要的话，可以将其设置为false
     * @var bool
     */
    protected $_customizeCallbackForOnce = true;

    public function __construct(Attacher $delivery)
    {
        $this->_thriftDelivery = $delivery;
    }

    public function setCustomizeCallbackCallStatus($status)
    {
        $this->_customizeCallbackForOnce = $status;
    }

    /**
     * 为 in service thrigt 特别设置
     * @param $value
     * @return $this
     */
    public function setServiceName($value)
    {
        if ($value) {
            $this->_serviceName = $value;
        }
        return $this;
    }

    /**
     * set request
     * @param $value
     * @return $this
     */
    public function setRequsetInfo($value)
    {
        if ($value) {
            $this->_requestInfo = $value;
        }
        return $this;
    }

    /**
     * set retry times
     * @param $value
     * @return $this
     */
    public function setRetry($value)
    {
        if ($value) {
            $this->_retry = $value;
        }
        return $this;
    }

    /**
     * set call params
     * @param $value
     * @return $this
     */
    public function setCall($value)
    {
        if ($value) {
            $this->_call = $value;
        }
        return $this;
    }

    public function setFormatResultCallback($callback)
    {
        $this->_formatResultCallback = $callback;
        return $this;
    }

    public function setExceptionThrowStatus($status)
    {
        $this->_isExceptionNeedThrow = $status;
    }

    protected function _defaultFormatResultCallback(&$result)
    {
        $finalData = $result ? json_decode($result, true) : [];
        if ($finalData && $finalData['succ'] && isset($finalData['data'])) {
            return $finalData['data'];
        }
        if ($finalData && isset($finalData['succ'])) {
            return $finalData['succ'];
        }
        if ($this->_isExceptionNeedThrow) {
            Log::error('thrift response err service:' . $this->_serviceName . ' function:' . $this->_functionName . ' args:' . json_encode($this->_args));
            throw new ThriftResponseException('thrift response null');
        }
        return false;
    }

    protected function _formatResult(&$result)
    {
        if (is_callable($this->_formatResultCallback)) {
            $finalResult = call_user_func($this->_formatResultCallback, $result);
            if ($this->_customizeCallbackForOnce) {
                $this->_formatResultCallback = null;
            }
            return $finalResult;
        }
        return $this->_defaultFormatResultCallback($result);
    }

    /**
     *
     * @param $methodName
     * @param $args
     * @return mixed|null
     */
    public function __call($methodName, $args)
    {
        $this->_functionName = $methodName;
        $this->_args = $args;
        if ($this->_retry) {
            $this->_thriftDelivery->retry($this->_retry);
        }
        if ($this->_serviceName) {
            $this->_thriftDelivery->call('call')
                ->with($this->_serviceName, $methodName, json_encode($args), $this->_requestInfo);
        } else {
            $this->_thriftDelivery->call($methodName);
            call_user_func_array(array($this->_thriftDelivery, 'with'), $args);
        }
        $result = $this->_thriftDelivery->run();
        return $this->_formatResult($result);
    }


}

