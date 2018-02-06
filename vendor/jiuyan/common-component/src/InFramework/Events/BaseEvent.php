<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/16
 * Time: 15:54
 */

namespace Jiuyan\Common\Component\InFramework\Events;

use Illuminate\Support\Collection;
use Jiuyan\Common\Component\InFramework\Exceptions\ServiceException;

class BaseEvent
{
    /**
     * 用以接收client模式下请求当中的公共参数。例如：_gps, _wifi, _platform, _source 等等
     * 有时在事件处理中，会用到某些公共参数的内容，因此这里将数据承接下来。
     * @var array
     */
    protected $_requestCommonParams = [];

    protected $_requestGeneralParams = [];

    protected $_generalParamsRules = [];

    public function setRequestCommonParams($params)
    {
        $this->_requestCommonParams = $params;
    }

    /**
     * @return array
     */
    public function getRequestCommonParasm()
    {
        return $this->_requestCommonParams;
    }

    public function setRequestGeneralParams($params)
    {
        if (!$this->_checkGeneralParams($params)) {
            throw new ServiceException('event init general params is invalid rule:' . json_encode($this->_generalParamsRules) . ' cur:' . json_encode($params));
        }
        $this->_requestGeneralParams = $params;
    }

    public function getRequestGeneralParams()
    {
        return $this->_requestGeneralParams;
    }

    /**
     * 检测params的内容是否符合要求
     * @param $generalParams
     * @return bool
     */
    protected function _checkGeneralParams(&$generalParams)
    {
        if ($this->_generalParamsRules && array_diff_key($this->_generalParamsRules, $generalParams)) {
            return false;
        }
        return true;
    }
}