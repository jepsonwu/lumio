<?php
/**
 * Created by IntelliJ IDEA.
 * User: XIAOQIANG
 * Date: 2014/7/5
 * Time: 18:22
 */

namespace In\Sms\Agent;
use In\Sms\config\SmsConfig;
class Tuopengsms extends BaseSmsAgent{

    protected $url = "http://121.199.48.186:1210/services/msgsend.asmx/";
    protected $user="";
    protected $pwd="";


    /**
     * 发送短信
     * @param string $type 短信的类型
     * @param string $mobile 接收信息的手机号
     * @param string $content 发送内容
     */
    public function send($type, $mobile, $content)
    {
        $target = $this->url."SendMsg";

        $mobile = preg_replace('/^086|^86/','',$mobile);

        if(empty($mobile)){
            return '手机号不能为空!';
        }

        $userCode = $this->user;
        $userPass = $this->pwd;
        $post_data = "userCode={$userCode}&userPass={$userPass}&DesNo=".$mobile."&Msg=".rawurlencode($content).'&Channel=1';
        $gets =  $this->xml_to_array($this->Post($post_data, $target));
        if(isset($gets['string']) && $gets > 0){
            return true;
        }else{
            return $gets;
        }
    }

    /**
     *
     * 语音验证码
     *
     * $type值说明
     * 1: 男生版“您的校验码”
     * 2: 女生版“您的校验码”
     * 3: 男生版“您的验证码”
     * 4: 女生版“您的验证码”
     * 5: 男生版“您的注册码”
     * 6: 女生版“您的注册码”
     *
     * @param $mobile 手机号
     * @param $code 验证码（只能是数字，长度1-6位）
     * @param int $times 播放次数（1-3）
     * @param int $type 模板语音发送（1-6）
     */
    public function voiceSend($mobile, $code, $times = 1, $type = 4) {
        $code = trim($code);
        $mobile = preg_replace('/^086|^86/','',$mobile);
        if(empty($mobile)) {
            throw new Exception('mobile required');
        }
        if(!preg_match('/^1\d{10}$/', $mobile)) {
            throw new Exception('invalid mobile');
        }
        if(empty($code)) {
            throw new Exception('code required');
        }
        if(!preg_match('/^\d{1,6}$/', $code)) {
            throw new Exception('invalid code');
        }
        if(!in_array($times, [1, 2, 3])) {
            throw new Exception('invalid times');
        }
        if(!in_array($type, [1, 2, 3, 4, 5, 6])) {
            throw new Exception('invalid type');
        }

        $userCode = 'hzjyyy';
        $userPass = 'hzjyyy459';
        $target = $this->url."SendVoiceCodeWithTemplate";
        $post_data = "userCode={$userCode}&userPass={$userPass}&DesNo=".$mobile."&VoiceCode=".$code."&Amount={$times}&TemplateID={$type}";
        $gets =  $this->xml_to_array($this->Post($post_data, $target));
        if(isset($gets['string']) && $gets['string'] > 0){
            return true;
        }else{
           return false;
        }

    }

    /**
     *
     * 批量获取上行短信
     * 获取1次，下次就获取不到了
     *
     * @return array
     */
    public function batchGetUpSms() {
        $target = "http://121.199.48.186:1210/services/msgsend.asmx/GetMo2";

        $userCode = $this->user;
        $userPass = $this->pwd;
        $post_data = "userCode={$userCode}&userPass={$userPass}";
        $gets = $this->xml_to_array($this->Post($post_data, $target));

        $result = array();
        if(isset($gets['string']) && is_string($gets['string'])){
            $rows = explode('|;|', $gets['string']);
            if(empty($rows)) {
               return $result;
            }

            foreach($rows as $row) {
                if(empty($row)) {
                    continue;
                }
                $arr = explode('|,|', $row);
                if(is_array($arr)) {
                    $arr = array_map('trim', $arr);
                    $result[] = array(
                        'mobile' => $arr[0] ? $arr[0] : '',
                        'content' => $arr[1] ? $arr[1] : '',
                        'time' => $arr[2] ? strtotime($arr[2]) : '',
                        'channel' => $arr[3] ? $arr[3] : '',
                    );
                }
            }
        }
        return $result;
    }

    function Post($curlPost,$url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 3);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        $return_str = curl_exec($curl);
        $curl_errno = curl_errno($curl);
        $curl_error = curl_error($curl);
        curl_close($curl);
        if($curl_errno >0){
            return "TuoPeng:cURL Error ($curl_errno): $curl_error\n";
        }else{
            return $return_str;
        }
    }
    function xml_to_array($xml){
        $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
        if(preg_match_all($reg, $xml, $matches)){
            $count = count($matches[0]);
            for($i = 0; $i < $count; $i++){
                $subxml= $matches[2][$i];
                $key = $matches[1][$i];
                if(preg_match( $reg, $subxml )){
                    $arr[$key] = $this->xml_to_array( $subxml );
                }else{
                    $arr[$key] = $subxml;
                }
            }
        }
        return $arr;
    }


    public function getText($textGp,$textArg=array()){
//        $text = array(
//            'in'=>'手机验证码：code，请勿将验证码告知他人【我的生活in记】',
//            'promo'=>'你的好友name在in里关注了你，马上去看看 url。退订回复TD【我的生活in记】',
//            'comment' => '你的好友name在in里面回复了你，去看看>url【我的生活in记】',
//            'watch' => '哇哦，name在in里面关注你了，快去in中看看ta是谁吧>url【我的生活in记】',
//        );
        $text = SmsConfig::$config['smstemplate'];
        $returnText = '';
        if(isset($text[$textGp])){
            $returnText = str_replace(array_keys($textArg),array_values($textArg),$text[$textGp]);
        }
        return $returnText;
    }

   
}