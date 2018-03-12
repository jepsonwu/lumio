<?php
/**
 * Created by PhpStorm.
 * User: ziliang
 * Date: 16/4/29
 * Time: 下午1:41
 */

namespace In\Sms;

use In\Sms\log\SmsLog;
use In\Sms\config\SmsConfig;

class SmsMobileCheck
{

    const IN_SMS_CACHE = "in_sms";
    const CAPTCHA_EXPIRE_TIME = 1800;

    private $memcacheObj;
    private $type = "";

    public function __construct($type = "in")
    {
        $this->updateAttr($type);
        $this->memcacheObj = new \Memcached();
        foreach (SmsConfig::$config['memcached'] as $info) {
            $this->memcacheObj->addServer($info['host'], $info['port'], true);
        }
    }

    public function updateAttr($type)
    {
        $this->type = $type;
    }

    /*
     * @param $mobile 手机号
     * desc:生成验证码
     */
    public function generalCaptchaCode($mobile = '')
    {
        //Sms::$appLogObj->put("test",0,'','sms');
        //Sms::$appLogObj->flush();
        $code = mt_rand(1074, 9915);
        return $code;
    }

    /*
     * @param $mobile 手机号
     * @param $code   验证码
     * desc:存储验证码
     */
    public function storageCaptchaCode($mobile, $code, AuthCodeInterface $storage)
    {
        $result = true;
        try {
            $codeInfo = $storage->find($mobile);
            $field = array('created_at' => time());
            if ($codeInfo) {
                $field['code'] = $code;
                $storage->update($mobile, $field);
            } else {
                $field['code'] = $code;
                $field['number'] = $mobile;
                $storage->add($field);
            }
        } catch (Exception $e) {
            $result = false;
        }
        return $result;

    }


    /*
     * @param $mobile 手机号
     * @param $internal 发送间隔的秒数
     * desc:验证手机号/防止盗刷(限制一小时内的发送次数和每次发送间隔为$internal)
     */
    public function checkMobile($mobile, $interval = 10)
    {
        $result = true;
        $msg = '手机格式和发送频率 验证通过';        //错误信息说明
        $sms_code = '';   //错误代码
        $mobile = $this->sanitizeNumber($mobile);
        $error = 0;
        $phone_key = $this->getSmsCacheKey($mobile, 'interval');
        $lastSendTime = $this->memcacheObj->get($phone_key);
        if ($lastSendTime && $lastSendTime + $interval > time()) {
            $result = false;
            $msg = '短时间内点击过频';
            $error = SmsConfig::ERR_USER_SMS_CODE_ALREADY_SENT;
        } else {
            $this->memcacheObj->set($phone_key, time());
        }

        //通过限制次数来防刷
        if ($mobile) {
            $smscode_limit_key = $this->getSmsCacheKey($mobile, 'day-send-' . date('Ymd'));

            $count = $this->memcacheObj->get($smscode_limit_key);
            if ($count >= 15) {
                $result = false;
                $msg = '已经超过一天的发送次数';
                $error = SmsConfig::ERR_USER_SMS_CODE_ALREADY_SENT;
            } else {
                if ($count == false) {
                    $this->memcacheObj->add($smscode_limit_key, 1, 3600);
                } else {
                    $this->memcacheObj->increment($smscode_limit_key, 1);
                }
            }
        } else {
            $result = false;
            $msg = '手机号码格式不对';
            $error = SmsConfig::ERR_USER_PHONE_FORMAT_ERROR;
        }

        $item = array('mobile' => $mobile, 'msg' => $msg);
        SmsLog::addSmsDebugLog($item);
        return array('success' => $result, 'msg' => $msg, 'error_code' => $error);

    }


    /*
     * @param mobile 手机号码
     * @param group 调用方标识
     * @param channel 发送短信的渠道
     * @param code   用户输入的验证码
     * desc 校验验证码
     */
    public function verifyCaptchaCode($mobile, $code, $channel, $group, AuthCodeInterface $storage)
    {

        $result = true;
        $codeInfo = $storage->find($mobile);
        $period = time() - $codeInfo['created_at'];
        if (!$code || !$codeInfo || ($codeInfo['code'] != $code) || ($period > self::CAPTCHA_EXPIRE_TIME)) {
            $result = false;
        }

        /*==========验证码 成功/失败统计========*/

        $item = SmsLog::queueItem($channel, $mobile, $group, 'CHECK');
        if ($result)
            $item['stat_type'] = "ok";
        else
            $item['stat_type'] = "fail";
        SmsLog::addVerifyCodeInfoToQ($item);
        /*=====================*/

        return array('success' => $result);

    }


    public function sanitizeNumber($number)
    {
//        $origin = $number;
        /** 先去空格和- +号*/
        $number = str_replace(array("-", " ", "+", "(", ")"), '', trim($number));

        $reverse = strrev($number);
        $length = strlen($number);

        // 只取 手机号
        if ($length >= 11) {
            $maybe_mobile = strrev(substr($reverse, 0, 11));
            if (preg_match('/^1[34578]\d{9}$/', $maybe_mobile) && $maybe_mobile != '13800138000') {  // 手机号 带上了 086 这样的前缀  排除13800138000 移动充值号码
                return $maybe_mobile;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    private function getSmsCacheKey($mobile, $tag = '')
    {
        return $this->type . "_V201_" . $tag . '_' . $mobile;
    }


}