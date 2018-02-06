<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2015/1/16
 * Time: 14:27
 */
namespace In\Sms\Agent;

use In\Sms\lib\montnets\Client;
use In\Sms\config\SmsConfig;

class Montnetssms extends BaseSmsAgent
{
    protected $url = "http://122.144.173.66:5102/mwgate/wmgw.asmx";
    protected $user = "send01";
    protected $pwd = "123456";


    /**
     * 发送短信
     * @param string $type 短信的类型
     * @param string $mobile 接收信息的手机号
     * @param string $content 发送内容
     */
    public function send($type, $mobile, $content)
    {
        // 梦网短信平
        //include_once("../lib/montnets/Client.php");
//            $smsInfo['server_url'] = 'http://ws.montnets.com:9003/MWGate/wmgw.asmx?wsdl';
//            $smsInfo['user_name'] = 'J02519';
//            $smsInfo['password'] = '522010';
        $smsInfo['server_url'] = $this->url;
        $smsInfo['user_name'] = $this->user;
        $smsInfo['password'] = $this->pwd;
        $smsInfo['pszSubPort'] = '*';
        $mobiles = array($mobile);
        $sms = new Client($smsInfo['server_url'], $smsInfo['user_name'], $smsInfo['password'], 3, 3);
        $sms->pszSubPort = $smsInfo['pszSubPort'];
        $sms->setOutgoingEncoding("UTF-8");
        $result = $sms->sendSMS($mobiles, $content);
//            header("Content-Type:text/plain;charset=utf-8");
        if ($result['status']) {
            return $result['status'];
        } else {
            return $result['msg'];
        }
    }

    /**
     *
     * 批量获取上行短信
     *
     * @return array
     */
    public function batchGetUpSms()
    {
        //include_once("lib/montnets/Client.php");
        $smsInfo['server_url'] = $this->url;
        $smsInfo['user_name'] = $this->user;
        $smsInfo['password'] = $this->pwd;
        $sms = new Client($smsInfo['server_url'], $smsInfo['user_name'], $smsInfo['password'], 3, 5);
        $sms->setOutgoingEncoding("UTF-8");
        $result = $sms->batchGetUpSms($smsInfo['user_name'], $smsInfo['password']);
        return $result;
    }

    /**
     *
     * 语音短信
     *
     * 语音模板编号对应内容
     * 100138: 欢迎使用，您的验证码是xxxx
     *
     * @param $mobile
     * @param $content
     * @param int $msgType 1:语音验证码  2:语音通知（文本语言）    3:语音通知（语音id）
     * @param int $tplId 语音模板编号
     * @return bool
     */
    public function voiceSend($mobile, $content, $msgType = 1, $tplId = 100138)
    {
        $content = trim($content);
        $mobile = preg_replace('/^086|^86/', '', $mobile);
        if (empty($mobile)) {
            throw new Exception('mobile required');
        }
        if (!preg_match('/^1\d{10}$/', $mobile)) {
            throw new Exception('invalid mobile');
        }
        if (empty($content)) {
            throw new Exception('content required');
        }
        if (!in_array($msgType, [1, 2, 3])) {
            throw new Exception('invalid msgType');
        }
        if (!in_array($tplId, [100138])) {
            throw new Exception('invalid tplId');
        }

        $url = 'http://61.145.229.28:5001/voiceprepose/MongateSendSubmit';
        $param = array(
            'userId' => 'YYC078',
            'password' => '215217',
            'pszMobis' => $mobile,
            'pszMsg' => $content,
            'PtTmplId' => $tplId,
            'msgType' => $msgType,
        );
        $paramQuery = http_build_query($param);
        $response = $this->Post($paramQuery, $url);

        if ($response !== false) {
            $result = $this->xml_to_array($response);
            if (is_numeric($result['string'])) {
                return true;
            } else {
                Yii::log("Montnetssms Send Voice Code Error: " . json_encode($result), CLogger::LEVEL_ERROR);
                return false;
            }
        }
        return false;
    }

    function xml_to_array($xml)
    {
        $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
        if (preg_match_all($reg, $xml, $matches)) {
            $count = count($matches[0]);
            for ($i = 0; $i < $count; $i++) {
                $subxml = $matches[2][$i];
                $key = $matches[1][$i];
                if (preg_match($reg, $subxml)) {
                    $arr[$key] = $this->xml_to_array($subxml);
                } else {
                    $arr[$key] = $subxml;
                }
            }
        }
        return $arr;
    }


    function Post($curlPost, $url)
    {
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
        if ($curl_errno > 0) {
            return false;
        } else {
            return $return_str;
        }
    }

    public function getText($textGp, $textArg = array())
    {
//            $text = array(
//                'in'=>'手机验证码：code，请勿将验证码告知他人。',
//                'promo' => '你的好友name在in里关注了你，马上去看看。url',
//                'comment' => '你的好友name在in里面回复了你，去看看>url【我的生活in记】',
//                'watch' => '哇哦，name在in里面关注你了，快去in中看看ta是谁吧>url【我的生活in记】',
//
//                'push_watch'=>'name 刚刚关注了你，点击查看 url',
//                'push_zan'=>'name 点赞了你的图片，点击查看 url',
//                'push_comment'=>'name 评论了你，点击查看 url',
//                'push_reply'=>'name 回复了你，点击查看 url',
//                'push_poke'=>'name at了你，点击查看 url',
//                'push_chat'=>'name 向你发送聊天请求，点击查看 url',
//                'push_feedcount'=>'你在in有count条最新动态,点击查看 url',
//
//                'push_watch_sys'=>'你有一条新的关注消息，点击查看 url',
//                'push_zan_sys'=>'你有一条新的点赞消息，点击查看 url',
//                'push_comment_sys'=>'你有一条新的评论消息，点击查看 url',
//                'push_reply_sys'=>'你有一条新的评论消息，点击查看 url',
//                'push_poke_sys'=>'有人在in里@了你，点击查看 url',
//                'push_chat_sys'=>'你有一条新的聊天请求，点击查看 url',
//                'promo_yuyue' => '亲爱的username，恭喜您成功通过in快速通道预约植村秀眉妆服务！请于month月day日到shop出示此条短信给柜台工作人员，即可享受植村秀免费修眉塑眉服务。如果需要修改预约日期，请在植村秀官方微信眉妆服务平台上重新预约（微信搜索公众号“植村秀”-关注后进去眉妆预约板块），谢谢。回复退订TD'
//            );
        $text = SmsConfig::$config['smstemplate'];
        $returnText = '';
        if (isset($text[$textGp])) {
            $returnText = str_replace(array_keys($textArg), array_values($textArg), $text[$textGp]);
        }
        return $returnText;
    }


}