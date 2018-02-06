<?php
/**
 * 助通科技的
 * User: xiaoqiang
 * Date: 2014/10/27
 * Time: 18:22
 */

namespace In\Sms\Agent;

use In\Sms\config\SmsConfig;

class ZhuTongsms extends BaseSmsAgent
{

    protected $url = "http://www.ztsms.cn:8800/sendXSms.do";
    protected $user = "jiuqikeji";
    protected $pwd = "Jq654321";
    public $productId = "71451";

    public $error = array(
        '-1' => '用户名或者密码不正确',
        '0' => '0发送短信失败,xxxxxxxx代表消息编号',
        '2' => '余额不够',
        '3' => '扣费失败（请联系客服）',
        '5' => '短信定时成功, xxxxxxxx代表消息编号',
        '6' => '有效号码为空',
        '7' => '短信内容为空',
        '8' => '无签名，必须，格式：【签名】',
        '9' => '没有Url提交权限',
        '10' => '发送号码过多,最多支持200个号码',
        '11' => '产品ID异常',
        '12' => '参数异常',
        '13' => '12小时重复提交',
        '14' => '用户名或密码不正确，产品余额为0，禁止提交，联系客服',
        '15' => 'Ip验证失败',
        '19' => '短信内容过长，最多支持500个',
        '20' => '定时时间不正确：格式：20130202120212(14位数字)',
    );

    public function setProductId($pid)
    {
        $this->productId = $pid;
    }


    /**
     * -1    用户名或者密码不正确
     * 1,xxxxxxxx    1代表发送短信成功,xxxxxxxx代表消息编号
     * 0,xxxxxxxx    0发送短信失败,xxxxxxxx代表消息编号
     * 2    余额不够
     * 3    扣费失败（请联系客服）
     * 5,xxxxxxxx    短信定时成功, xxxxxxxx代表消息编号
     * 6    有效号码为空
     * 7    短信内容为空
     * 8    无签名，必须，格式：【签名】
     * 9    没有Url提交权限
     * 10    发送号码过多,最多支持200个号码
     * 11    产品ID异常
     * 12    参数异常
     * 13    12小时重复提交
     * 14    用户名或密码不正确，产品余额为0，禁止提交，联系客服
     * 15    Ip验证失败
     * 19    短信内容过长，最多支持500个
     * 20    定时时间不正确：格式：20130202120212(14位数字)
     *
     *
     * 发送短信
     * @param string $type 短信的类型
     * @param string $mobile 接收信息的手机号
     * @param string $content 发送内容
     */
    public function send($type, $mobile, $content)
    {

        $target = $this->url;
        $mobile = preg_replace('/^086|^86/', '', $mobile);
        if (empty($mobile)) {
            return '手机号不能为空!';
        }
        $username = $this->user;
        $password = $this->pwd;
        $tkey = $this->gettKey();
        //"您的验证码是：".$mobile_code."。请不要把验证码泄露给其他人。"
//        $post_data = "username={$username}&password={$password}&mobile=" . $mobile . "&content=" . urlencode($content) . "&productid=" . $this->productId;//973785//95533,666999,136136
        $post_data = ['username'=>$username,'password'=>$password,'mobile'=>$mobile,'tkey'=>$tkey,'content'=>$content,'productid'=>$this->productId];
        $post_data['password'] = $this->encryCode($password, $tkey);
        $query = http_build_query($post_data);
        //密码可以使用明文密码或使用32位MD5加密
        $gets = $this->Post($query, $target);

        $return = false;
        if (strpos($gets, ',') !== false) {
            $returnArr = explode(',', $gets);
            if (isset($returnArr[0]) && $returnArr[0] == 1) {
                $return = true;
            } elseif (isset($returnArr[0]) && isset($this->error[$returnArr[0]])) {
                $return = $this->error[$returnArr[0]];
            }
        } elseif (isset($this->error[$gets])) {
            $return = $this->error[$gets];
        }
        return $return;
    }

    function Post($curlPost, $url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        curl_setopt($curl, CURLOPT_TIMEOUT, 3);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
        $return_str = curl_exec($curl);
        $curl_errno = curl_errno($curl);
        $curl_error = curl_error($curl);
        curl_close($curl);
        if ($curl_errno > 0) {
            return "ZhuTong:cURL Error ($curl_errno): $curl_error\n";
        } else {
            return $return_str;
        }
    }

    public function getText($textGp, $textArg = array())
    {
//        $text = array(
//            'in'=>'手机验证码：code，请勿将验证码告知他人。'
//        );
        $text = SmsConfig::$config['smstemplate'];
        $returnText = '';
        if (isset($text[$textGp])) {
            $returnText = str_replace(array_keys($textArg), array_values($textArg), $text[$textGp]);
        }
        return $returnText;
    }


    public function gettKey(){
        return date("YmdHis");
    }

    public function encryCode($pwd,$tKey){
        return md5(md5($pwd).$tKey);
    }


} 