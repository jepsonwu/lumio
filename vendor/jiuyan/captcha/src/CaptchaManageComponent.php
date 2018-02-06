<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/9
 * Time: 16:56
 */

namespace Jiuyan\Captcha;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Jiuyan\CommonCache\InterfaceBanyan;
use Log;

class CaptchaManageComponent
{
    /**
     * @var CacheRepository
     */
    protected $_cacheRepository = null;

    /**
     * @var SendCaptchaContract
     */
    protected $_captchaSendHandle = null;

    public $handleType = '';

    public function generateCaptcha($targetFlag, $cateFlag = '')
    {
        return mt_rand(1074, 9915);
    }

    public function setCaptchaHandle($handleType)
    {
        $this->handleType = $handleType;
        $handleName = "Jiuyan\\Captcha\\Components\\" . ucfirst($handleType) . 'CaptchaComponent';
        if (class_exists($handleName)) {
            $this->_captchaSendHandle = new $handleName();
        }
        return $this;
    }

    /**
     * 发送成功后，会将当前验证码返回
     * @param string $targetFlag 发送目标标识：手机号，邮箱等等
     * @param string $cateFlag 业务标识
     * @param string $captchaHandleName 发送方式
     * @return bool
     */
    public function sendCaptcha($targetFlag, $cateFlag = '', $captchaHandleName = 'sms')
    {
        $this->setCaptchaHandle($captchaHandleName);
        $captchaCode = $this->_captchaSendHandle->sendCaptcha($targetFlag, $cateFlag);
        if ($captchaCode && $this->_dealCaptcha('set', $cateFlag, $targetFlag, $captchaCode)) {
            return $captchaCode;
        }
        return false;
    }

    /**
     * @return CacheRepository|mixed|InterfaceBanyan
     */
    protected function _getCacheRepository()
    {
        if (is_null($this->_cacheRepository)) {
            $this->_cacheRepository = app(config('captcha.cache.repository', 'cache'));
        }
        return $this->_cacheRepository;
    }

    public function verifyCaptcha($currentCaptcha, $targetFlag, $cateFlag = '')
    {
        $realCaptcha = $this->_dealCaptcha('get', $cateFlag, $targetFlag);
        if (!$realCaptcha || $realCaptcha != $currentCaptcha) {
            Log::error('captcha verify failed rc:' . $realCaptcha . ' cc:' . $currentCaptcha . ' tg:' . $targetFlag . ' cf:' . $cateFlag . ' hd:' . $this->handleType);
            return false;
        }
        return true;
    }

    protected function _dealCaptcha($ope, $cateFlag, $mobile, $captcha = '')
    {
        $cacheKey = "captcha_ck_{$cateFlag}_{$mobile}";
        switch ($ope) {
            case 'set':
                $expireTime = config('captcha.cache.expire', 900);
                return $this->_getCacheRepository()->set($cacheKey, $captcha, $expireTime);
                break;
            case 'get':
                return $this->_getCacheRepository()->get($cacheKey);
                break;
            default:
                break;
        }
    }
}
