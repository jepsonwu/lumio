<?php
/**
 * Created by PhpStorm.
 * User: zilaing
 * Date: 16/5/5
 * Time: 下午1:52
 */

/*
 * 返回配置项
 */
namespace In\Sms\config;
use In\Sms\Sms;

class SmsConfig
{


    static function loadConfig($type){
        $content = include __DIR__."/".$type.".php";
        if($content) {
            self::$config = array_merge(self::$config,$content);
        }

        if(Sms::$soaConfigGlobal) {
            self::$config['memcached'] = Sms::$soaConfigGlobal['memcached']['sms'];
            self::$config['mq']['sysqueue'] = Sms::$soaConfigGlobal['rabbitmq']['sms'];
            self::$config['mq']['send_sms_log'] = Sms::$soaConfigGlobal['rabbitmq']['sms'];
        }
    }

    static $config = array(

        //指定发短信的模板
        "smstemplate"=>array(
            'in'=>'手机验证码：code，请勿将验证码告知他人。',
            'promo' => '你的好友name在in里关注了你，马上去看看。url',
            'comment' => '你的好友name在in里面回复了你，去看看>url',
            'watch' => '哇哦，name在in里面关注你了，快去in中看看ta是谁吧>url',

            'push_watch'=>'name 刚刚关注了你，点击查看 url',
            'push_zan'=>'name 点赞了你的图片，点击查看 url',
            'push_comment'=>'name 评论了你，点击查看 url',
            'push_reply'=>'name 回复了你，点击查看 url',
            'push_poke'=>'name at了你，点击查看 url',
            'push_chat'=>'name 向你发送聊天请求，点击查看 url',
            'push_feedcount'=>'你在in有count条最新动态,点击查看 url',

            'push_watch_sys'=>'你有一条新的关注消息，点击查看 url',
            'push_zan_sys'=>'你有一条新的点赞消息，点击查看 url',
            'push_comment_sys'=>'你有一条新的评论消息，点击查看 url',
            'push_reply_sys'=>'你有一条新的评论消息，点击查看 url',
            'push_poke_sys'=>'有人在in里@了你，点击查看 url',
            'push_chat_sys'=>'你有一条新的聊天请求，点击查看 url',
            'promo_yuyue' => '亲爱的username，恭喜您成功通过in快速通道预约植村秀眉妆服务！请于month月day日到shop出示此条短信给柜台工作人员，即可享受植村秀免费修眉塑眉服务。如果需要修改预约日期，请在植村秀官方微信眉妆服务平台上重新预约（微信搜索公众号“植村秀”-关注后进去眉妆预约板块），谢谢。回复退订TD',
            'promo_school' => 'n校园推广支付成功！二维码地址：url_ticket，在in中扫描并领取优惠券，下单购买直接抵扣！回复TD退订',
            'deliver-got' => '发货啦！亲，您的订单已打包发货，献上运单号：waybill。前往In看看宝贝运到哪儿啦~请戳 url 回复TD退订',
            'promo_pandora' => '恭喜您获得潘多拉专属礼品，凭此短信到潘多拉指定门店让柜员打开 url 点击确认，即可领取！回复TD退订',
            'return_order' => '抱歉久等了！您的货物已经重寄，重寄单号是：waybill(company)，前往in获取最新的物流状态~请戳http://dwz.cn/3LJXIu。回复TD退订！',
            'doudou_sale' => '你关注的商品 title 即将开抢，不要错过哦【兜兜特卖】',
            'doudou_sale_unsubscribe'=>'你关注的商品 title 即将开抢，不要错过哦 退订回N【兜兜特卖】',

            'live_onehour' => '好多粉丝订阅了你的title直播呢！今日time点，和粉丝们准时相见吧！回TD退订',
            'live_tomorrow' => '宝宝~还记得我们约定好的title直播吗？明天time点，等你哟~回TD退订',
            'live_can_go_hot' => 'name 正在直播 desc，已达热门标准，快起来补包阿！回TD退订',
            'live_can_go_discover' => '太nb啦，name 正在直播 desc，已达发现标准，快起来推阿！回TD退订',
            'live_join_white' => '宝宝，你的直播成功引起了小编的注意！来小编怀里，让你成为直播巨星！！（加小编qq：2942497483）',
            
            'in_printer_vip'=>'亲爱的黄金会员，您的订单已经打包发货啦，献上物流单号：waybill~前往in查看宝贝位置请戳 url',
            'in_printer_spring' => '亲爱的用户，恭喜您成功下单。过年期间，生产发货暂停，我们将于2月7日开始恢复生产发货。如有疑问可微信关注【吾印】，向我们咨询,回TD退订',
            'in_printer_draw' => '有新订单啦！购买插画风格：style；in昵称：user_nickname；in号：user_in_no，请及时查看qq回复买家哦～',
            'in_daian' => '【黛安芬 · 终于找对TA】您已成功领取黛安芬热力小裤一条，奖品以现场实际领取为准，请于2017/4/30之前，前往北京、上海、广州、杭州、成都、武汉、深圳的任一城市指定专柜领取，到柜后请向门店人员出示链接url 回复TD退订',
            'in_yuxi' => '您已成功领取羽西全新升级白芍淡斑系列3件套，可于2017/04/22-2017/04/30前往全国指定的羽西专柜领取，凭此链接使用  url 回复TD退订'

        )

    );

    const ERR_USER_PHONE_FORMAT_ERROR = 20107; // 手机号格式不对
    const ERR_USER_SMSCODE_SEND_FAILED = 20113; // 短信验证码发送失败
    const ERR_USER_SMS_CODE_ALREADY_SENT = 20114; // 短信验证码已经发送

}