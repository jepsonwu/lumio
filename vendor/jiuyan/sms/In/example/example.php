<?php
/**
 * Created by PhpStorm.
 * User: ziliang
 * Date: 16/5/12
 * Time: 下午6:30
 */

namespace In\example;
use In\Sms;

$path =  dirname(dirname(__DIR__));
require  $path."/vendor/autoload.php";
$mobile = "18768143593";
$res = Sms\Sms::sendSms($mobile, "in", [], "in","HCloud");
var_dump($res);
die;
$param = [];
$param = array('name'=>'ziliang','url'=>'www.in66.com');
$res = Sms\Sms::sendSms($mobile, "push_watch", $param, "in","HCloud");
var_dump($res);
die;
/*============手机格式验证和控制发送频率==========*/
$mobile = "23235";
Sms\Sms::checkMobile($mobile);
$res = Sms\Sms::checkMobile($mobile);
/*
 * $res 格式:array('succ'=>true/false,'msg'=>'短时间内点击过频');
 */
print_r($res);
echo "\n";


/*=================返回生成的验证码===============*/
$mobile = "18768143593";
$code = Sms\Sms::generalCode($mobile);
echo "{$mobile}的验证码:".$code."\n";


/*============短信发送渠道选择,返回被选中的渠道======*/

/*
 * $mobile 手机号码
 * $textKey 调用方身份标示
 * $param 一些额外的参数 如code:验证码
 * return $res格式: array('success'=>true,'channel'=>"ZhuTong",'msg'=>"一些可用不可用的渠道信息");
 */
$res = Sms\Sms::sendChannel($mobile,"in",array('code'=>$code));
print_r($res);
echo "\n";


/*============短信的发送============================*/
/*
 * $mobile 手机号码
 * $textKey 调用方身份标示
 * $param 一些额外的参数 如code:验证码
 * $channel:"ZhuTong"
 * return $res格式: array('success'=>true,'channel'=>,'msg'=>"一些说明信息");
 */
$res = Sms\Sms::sendSms($mobile,"in",array('code'=>$code),"ZhuTong");
print_r($res);