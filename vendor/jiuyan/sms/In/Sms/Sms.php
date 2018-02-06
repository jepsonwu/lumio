<?php
/**
 * Created by PhpStorm.
 * User: ziliang
 * Date: 16/5/6
 * Time: 上午6:06
 */
namespace  In\Sms;
use In\Sms\config\SmsConfig;
use In\Sms\SmsMobileCheck;
use In\Sms\SmsSendChannel;
ini_set('date.timezone','Asia/Shanghai');
class Sms{


    static $appLogObj;
    static $soaConfigGlobal;

    static $checkObj;
    static $sendObj;

    /*
     * @param type: 调用短信服务的项目标识(如in,forme; 跟SmsConfig::$config配置对应)
     */
    static function init($type='in'){
        $resetConfig['smstemplate'] = SmsConfig::$config['smstemplate'];
        SmsConfig::$config = $resetConfig ;
        SmsConfig::loadConfig($type);

        if(!self::$checkObj) {
            self::$checkObj = new SmsMobileCheck($type);
        }else{
            self::$checkObj->updateAttr($type);
        }
        if(!self::$sendObj) {
            self::$sendObj = new SmsSendChannel($type);
        }else{
            self::$sendObj->updateAttr($type);
        }

    }

    /*
     * 过滤手机
     *
     */
    static function checkMobile($mobile,$type){
        self::init($type);
        return self::$checkObj->checkMobile($mobile);

    }

    /*
     * 生成验证码
     */
    static function generalCode($mobile){

        try {
            self::init();
            return self::$checkObj->generalCaptchaCode($mobile);
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /*
     * 保存验证码
     */
    static function saveCode($mobile, $code, AuthCodeInterface $storage){
        self::init();
        return self::$checkObj->storageCaptchaCode($mobile,$code,$storage);
    }

    /*
     * 验证验证码
     */
    static function verifyCode($mobile,$code,$channel,$group,AuthCodeInterface $storage){
        self::init();
        return self::$checkObj->verifyCaptchaCode($mobile,$code,$channel,$group,$storage);
    }

    /*
     * 发送渠道选择
     */
    static function sendChannel($mobile,$textKey,array $param,$type){
        self::init($type);
        return self::$sendObj->selectedChannel($mobile,$textKey,$param);
    }

    /*
     * 发送短信
     */
    static function sendSms($mobile,$textKey,array $param,$type,$channel=""){
        self::init($type);
        if($type == "promo")
            return self::$sendObj->sendPromo($mobile,$textKey,$param,$channel);  //活动短信
        else
            return self::$sendObj->sendSms($mobile,$textKey,$param,$channel);   //验证码
    }


}