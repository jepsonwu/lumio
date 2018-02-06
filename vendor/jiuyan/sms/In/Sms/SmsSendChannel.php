<?php
/**
 * Created by PhpStorm.
 * User: Ziliang
 * Date: 16/4/29
 * Time: 下午1:15
 */

namespace In\Sms;
use In\Sms\Agent;
use In\Sms\log\SmsLog;
use In\Sms\config\SmsConfig;
use PhpAmqpLib\Channel\AMQPChannel;

class SmsSendChannel{

    const CAPTCHA_EXPIRE_TIME = 1800;
    private $memcacheObj;

    private $usableChannel;  //可用的渠道:'Montnets','HuYi','Bech','ManDao','TuoPeng','ZhuTong'
    private $channel="";
    private $channel_ratio;
    //private $channel_ratio = array('Montnets'=>'60','TuoPeng'=>'5','ZhuTong'=>'35');

    private $item_sms_time = 600;
    public $veto_channel_time = 3600;    //单个渠道屏蔽的时间限制
    public $detection_time = 3600;  //国定时间内,检查渠道的失败次数

    private $mobile;
    private $texyKey;
    private $extra;
    private $type;
    public $count = 10;   //某个渠道出问题的次数

    public function __construct($type="in")
    {

        $this->updateAttr($type);
        $this->memcacheObj = new \Memcached();
        foreach (SmsConfig::$config['memcached'] as $info){
            $this->memcacheObj->addServer($info['host'], $info['port'], true);
        }

    }

    public function updateAttr($type){
        $this->type = $type;
        $this->usableChannel = SmsConfig::$config['channel'];
        $this->channel_ratio = SmsConfig::$config['channelRatio'];
        $this->checkObj = new SmsMobileCheck($type);
    }




    /*
     * @param $mobile 手机号码
     * @param $textKey 调用方身份标示
     * @param $param 一些额外的参数 如code:验证码
     * desc:选择发送短息的服务商
     */
    public function selectedChannel($mobile,$textKey,array $param){

        //保存必要信息,为发送短信函数做准备
        $this->mobile = $mobile;
        $this->texyKey = $textKey;
        $this->extra = $param;

        //debug信息数组
        $msg = array();
        $debugInfo = array('mobile'=>$mobile,'textKey'=>$textKey,'msg'=>'');

        $succ = true;
        //手机+内容为key,key不能重复
        $arr = array('mobile'=>$mobile,'textKey'=>$textKey,'extra'=>$param);
        try {
            $sms_cacheKey = $this->generalSmSContentKey($arr);
            $sms_result_value = $this->memcacheObj->get($sms_cacheKey);
            if ($sms_result_value == false)
                $sms_result_value = array();
            $VetoChannel = $this->getVetoChannel();

            if ($sms_result_value) {

                $sms_result_value = array_unique(array_merge($sms_result_value, $VetoChannel));
                if (count($this->usableChannel) == count($sms_result_value)) {   //如果这个号码，这个内容将所有的渠道都用完，就记录下来
                    /*================debug msg==========================*/
                    $msg[]="send_sms ---- be defeated! 所有发送渠道用尽!";
                    $succ = false;
                }

            } else {

                $sms_result_value = $VetoChannel;

            }

            if ($succ) {

//                $usable_channelArr = $this->usableChannel;
//                shuffle($usable_channelArr);      //打乱可用数组
//                $usable_channelArr = $this->getChannelratio($usable_channelArr);  // 按权重比排序
                $usable_channelArr = $this->shuffleChannel($this->usableChannel);
                foreach ($usable_channelArr as $res) {
                    if (!in_array($res, $sms_result_value)) {
                        $this->channel = $res;
                        $sms_result_value[]=$res;
                        $this->memcacheObj->set($sms_cacheKey,$sms_result_value,$this->item_sms_time);
                        break;

                    }
                }

            }
        }catch(Exception $e){
            $succ = false;
        }

        /*================debug msg==========================*/
        $msg[]= "usable_channel:".json_encode($this->usableChannel)." sms_result_value:".json_encode($sms_result_value)." veto_channel".json_encode($VetoChannel);

        foreach($msg as $m) {
            $debugInfo['msg'] = $m;
            SmsLog::addSmsDebugLog($debugInfo);
        }
        /*================debug msg==========================*/
        return array('success'=>$succ,'channel'=>$this->channel,'msg'=>$msg);

    }

    /**
     * @param $usableChannle
     * desc 按照权重排序可用渠道
     */
    public function shuffleChannel($usableChannle){
        $channls = $usableChannle;
        shuffle($channls);
        $new_channels = $this->getChannelratio($channls);
        return $new_channels;
    }

    public function sendSms($mobile,$textKey,array $param,$channel=""){
        $repeat = 0;
        $result = array();
        //首先验证
        $cres = $this->checkObj->checkMobile($mobile);
        //通过验证后
        if($cres['success']){
            //随机验证码
            $code = $this->checkObj->generalCaptchaCode();
            $param['code'] = $code;
            //发送
            $result = $this->send($mobile,$textKey,$param,$channel);
            //失败重新发送一次
            if(!$result['success'] && ($repeat == 0)){
                $result = $this->send($mobile,$textKey,$param,$channel);
                $repeat++;
            }
        }else{
            $result = $cres;
        }
        return $result;
    }

    /*
     * $assign 指定发送渠道和用户密码 例如:SmsConfig::$config['assign']['forme']
     * @desc 验证码发送
     */
    public function send($mobile,$textKey,array $param,$channel=""){
        //debug信息数组
        $msg = array();
        $info = '验证码发送成功';
        $error = 0;
        $debugInfo = array('mobile'=>$mobile,'textKey'=>$textKey,'msg'=>'');

        try {
            /*
             * 决定发送渠道
             */
            if(empty($channel)){
                $cres = $this->selectedChannel($mobile, $textKey, $param);
                if($cres['success'])
                    $channel = $cres['channel'];
            }
            /*
             * 发送
             */
            if($channel) {
                /*=============statics:请求发送统计=============*/
                $item = SmsLog::queueItem($channel, $mobile, $textKey, "SEND");
                $item['stat_type'] = 'send';
                SmsLog::addSmsCodeInfoToQ($item);
                /*============================*/
                //$channel = "Montnets";
                $smsstr = "In\\Sms\\Agent\\".$channel."sms";
                $sms = new $smsstr;
                $sms->setAccount(SmsConfig::$config['channelAccount'][$channel]['user'],
                    SmsConfig::$config['channelAccount'][$channel]['pwd']);
                $sms->setUrl(SmsConfig::$config['channelAccount'][$channel]['url']);

                $content = $sms->getText($textKey, $param);
                $result = $sms->send('', $mobile, $content);
                /*=============statics:发送成功/失败统计=============*/
                $item = SmsLog::queueItem($channel, $mobile, $textKey, "SEND");
                if ($result == 1 || $result === true) {
                    $result = true;
                }else {
                    $result = false;
                }
                SmsLog::addSmsCodeInfoToQ($item);
                /*============================*/
                if (!$result) {
                    /*
                     * 失败需要将失败的渠道做记录;
                     * 渠道失效次数达到一定上限,就将此渠道屏蔽一段时间
                     */
                    $channel_Key = $this->getMemcheSmsChannelKey($channel);
                    $channel_result = $this->memcacheObj->get($channel_Key);
                    if ($channel_result !== false) {

                        $this->memcacheObj->increment($channel_Key, 1);
                        if ($channel_result >= $this->count) {

                            //************debug log***************
                            $msg[] = " send  be defeated! veto! selected_channel:" . $channel;
                            //************************************
                            $this->memcacheObj->delete($channel_Key);    //删除此渠道的失效计数器,在下一步直接屏蔽它

                            $veto_channel_Key = $this->getMemcheVetoChannel($channel);
                            $this->memcacheObj->set($veto_channel_Key, 1,$this->veto_channel_time);    //失效频道屏蔽时间

                        } else {
                            //**************debug log*************
                            $msg[] = " send  be defeated! increment! selected_channel:" . $channel;
                            //************************************
                        }

                    } else {
                        //**************debug log*************
                        $msg[] = "send  be defeated! set! selected_channel:" . $channel;
                        //************************************
                        $cheannel_init = $this->memcacheObj->add($channel_Key, 1,$this->detection_time);
                        if (!$cheannel_init) {
                            $this->memcacheObj->increment($channel_Key, 1);
                        }
                    }
                    $info = "验证码发送失败";
                    $error = SmsConfig::ERR_USER_SMSCODE_SEND_FAILED;
                }
            }else{
                $info = "短时间内发送过多,请稍后再试!";
                $error = SmsConfig::ERR_USER_SMS_CODE_ALREADY_SENT;
                $result = false;
            }
        }catch(Exception $e){
            $result = false;
        }

        /*===================debug msg====================*/
        foreach($msg as $m) {
            $debugInfo['msg'] = $m;
            try {
                SmsLog::addSmsDebugLog($debugInfo);
            }catch (\Exception $e){

            }
        }
        $item = array('mobile'=>$mobile,'msg'=>$info);
        SmsLog::addSmsDebugLog($item);

        /*================================================*/

        return  array('success'=>$result,'msg'=>$info,'error_code'=>$error,'code'=>$param['code'],
            'info'=>array('channel'=>$channel,'mobile'=>$mobile,'textKey'=>$textKey,'extra'=>$param));

    }

    /**
     * @param $mobile
     * @param $textKey
     * @param array $param
     * @param string $channle
     * desc: 营销短信
     */
    public function sendPromo($mobile, $textKey, array $param, $channel=""){

        $result = false;
        //debug信息数组
        $msg = array();
        $info = '验证码发送成功';
        $error = 0;
        $debugInfo = array('mobile'=>$mobile,'textKey'=>$textKey,'msg'=>'promo '.$this->type);

        try {
            SmsLog::addSmsDebugLog($debugInfo);

            /*
             * 决定发送渠道
             */
            if(empty($channel)){
                $channels = $this->shuffleChannel($this->usableChannel);
                $channel = array_shift($channels);
                $debugInfo['msg'] = 'promo usableChannel:'. implode(',',$this->usableChannel);
                SmsLog::addSmsDebugLog($debugInfo);
                if ($channel == 'XinYiChen') {
                    $channel = 'Montnets';
                    $debugInfo['msg'] = 'promo usableErrorChannel:'. implode(',',$this->usableChannel);
                    SmsLog::addSmsDebugLog($debugInfo);
                }
            }
            /*
             * 发送
             */
            if($channel) {


//              $channel = "Montnets";
//              $channel = "ZhuTong";
                $smsstr = "In\\Sms\\Agent\\".$channel."sms";
                $sms = new $smsstr;
                if($channel == "ZhuTong")
                    $sms->setProductId(SmsConfig::$config['channelAccount'][$channel]['productid']);
                $sms->setAccount(SmsConfig::$config['channelAccount'][$channel]['user'],
                    SmsConfig::$config['channelAccount'][$channel]['pwd']);
                $sms->setUrl(SmsConfig::$config['channelAccount'][$channel]['url']);

                $content = $sms->getText($textKey, $param);
                $res = $sms->send('', $mobile, $content);
                /*=============statics:发送成功/失败统计=============*/
                if ( $res == 1 || $res === true){
                    $result = true;
                }else
                    $result = false;
                /*============================*/
            }else{
                $info = "发送失败!";
                $error = SmsConfig::ERR_USER_SMSCODE_SEND_FAILED;
            }
        }catch(Exception $e){

        }

        /*===================debug msg====================*/
        $debugInfo['msg'] = "营销类短信".$info;
        SmsLog::addSmsDebugLog($debugInfo);

        /*================================================*/

        return  array('success'=>$result,'msg'=>$info,'error_code'=>$error,'code'=>0,
            'info'=>array('channel'=>$channel,'mobile'=>$mobile,'textKey'=>$textKey,'extra'=>$param));
    }

    public function generalSmSContentKey($param){
        //手机+内容为key不能重复
        $str = $param['mobile'].'_'.$param['textKey'].'_'.md5(json_encode(array_keys($param['extra'])));
        $sign = $this->getMemcheSendSmsKey($str);
        return $sign;
    }

    /*
     * desc:获得全部的失效渠道
     */
    public function getVetoChannel(){

        $veto_channel = array();
        foreach($this->usableChannel as $channel){
            $vetoChannelKey = $this->getMemcheVetoChannel($channel);
            $sms_result = $this->memcacheObj->get($vetoChannelKey);
            if($sms_result == 1){
                $veto_channel[] = $channel;
            }
        }
        return $veto_channel;
    }




    /**
     * desc:短信发送渠道的优先级排序
     */
    private function getChannelratio($channel){
        if(count($channel) < 2){
            return $channel;
        }
        $ok_channel = array();
        $rand_max = 0;
        $rand_count = 0;
        foreach($this->channel_ratio as $channel_key=>$ratio){
            if(in_array($channel_key,$channel)){    //获得现在有用的渠道以及他的权重值

                $rand_max = $ratio + $rand_max;
                $key = $rand_max;

                $rand_count = $rand_count+$ratio;

                $ok_channel[$key] = $channel_key;
            }
        }

        $returnData = array();
        $rand = rand(1,$rand_count);
        foreach($ok_channel as $ratio_value=>$channel_value){
            if($rand <= $ratio_value){
                $returnData[] = $channel_value;
                unset($ok_channel[$ratio_value]);
                break;
            }
        }
        $returnData = array_merge($returnData,$ok_channel);
        return $returnData;
    }

    /**
     * 短信发送情况存储memcha的key
     */
    private function getMemcheSendSmsKey($number_text){
        return $this->type.'_SENDSMS_V1200_NUMBER_'.$number_text;
    }


    /**
     * 渠道存储memcha的key
     */
    private function getMemcheSmsChannelKey($channel){
        return $this->type.'_SENDSMS_V1200_CHANNEL_'.$channel;
    }

    /**
     * 失效渠道存储memcha的key
     */
    private function getMemcheVetoChannel($channel){
        return $this->type.'_SENDSMS_V1200_VETO_CHANNEL_'.$channel;
    }




}