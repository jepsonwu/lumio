<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 14-9-17
 * Time: 上午10:01
 */
namespace In\Sms\Agent;
use In\Sms\config\SmsConfig;
class Itugosms extends BaseSmsAgent{

    protected $url = "http://in.itugo.com/?r=api/addsms";
    protected $user='';
    protected $pwd='';


    /**
     * 发送短信
     * @param string $type 短信的类型
     * @param string $mobile 接收信息的手机号
     * @param string $content 发送内容
     */
    public function send($type, $mobile, $content)
    {
        //if($type == 'test'){
            $url = $this->url."&mobile={$mobile}&content={$content}";

            //请求接口，将这条消息插入到mq队列中
            try{
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $ret = curl_exec($ch);
                curl_close($ch);
            }catch (Exception $e){
                throw new Exception("An Error Occured");
            }
            $retArr = json_decode($ret,true);
            if(isset($retArr['data']['code']) && $retArr['data']['code'] == 1){
                return true;
            }else{
                return $retArr;
            }
        /*}else{
            return false;
        }*/
    }

    public function getText($textGp,$textArg=array()){
//        $text = array(
//            'in'=>'手机验证码：code，请勿将验证码告知他人。【分享时尚-in】'
//        );
        $text = SmsConfig::$config['smstemplate'];
        $returnText = '';
        if(isset($text[$textGp])){
            $returnText = str_replace(array_keys($textArg),array_values($textArg),$text[$textGp]);
        }
        return $returnText;
    }

   
}