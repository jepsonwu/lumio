<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/12/8
 * Time: 20:41
 */

namespace Modules\Account\Services;

use Jiuyan\Common\Component\InFramework\Services\BaseService;

class UserProfileService extends BaseService
{
    protected $_inUserService;

    public function __construct()
    {
        $this->_inUserService = app('InUserService');
    }

    /**
     * 隐藏自己，不要让别人通过手机号搜索到自己
     * @param $userId
     * @param $opeFlag: 1: 想被搜索到 0：不想
     * @return bool
     */
    public function changeMobileSearchStatus($userId, $opeFlag = false)
    {
        /**
         * 如果未指定操作方式，则不进行相关属性的修改
         */
        if ($opeFlag === false) {
            return true;
        }
        /**
         * 想被搜索到，则不隐藏手机
         */
        $hideOpe = $opeFlag ? false : true;
        return $this->_inUserService->hideMobile($userId, $hideOpe);
    }
}