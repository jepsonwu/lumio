<?php
/**
 * Created by PhpStorm.
 * User: Ziliang
 * Date: 16/4/29
 * Time: 下午8:42
 */

namespace In\Sms\log;

use In\Sms\config\SmsConfig;
use In\Sms\mq\MQProducer;
use Log;

/*
 * desc: sms的日志分为两种:第一种写入队列 作为统计信息;第二种直接作为调试追踪的log
 */

class SmsLog implements SmsLogInterface
{

    public static $mq;

    /*
     * @param $chanenl:短信服务商
     * @param $mobile:手机号
     * @param $group: 调用方的标识
     * @param $sms_type: 代表CHECK/SEND 区分是验证码/短信
     * @param stat_type:
     *          send:代表要求发送短信
     *          ok:代表发送成功s
     *          fail:代表发送失败
     * desc: 推送至队列中的元数据
     */


    static function getConnection($queue)
    {
        $config = SmsConfig::$config['mq'][$queue];

        try {
            if (!isset(self::$mq[$queue])) {
                self::$mq[$queue] = new MQProducer(
                    $config['host'],
                    $config['port'],
                    $config['user'],
                    $config['pass'],
                    $config['vhost'],
                    $config['debug'],
                    false, 'AMQPLAIN', null, 'en US',
                    $config['connection_timeout'],
                    $config['read_write_timeout']
                );
            }

            self::$mq[$queue]->set_queue_name_space('in');
        } catch (\Exception $e) {
            print_r($e->getMessage());
        }
        return self::$mq[$queue];
    }

    /**
     * 添加queue
     * @param array $params
     * @param unknown_type $queue
     * @param unknown_type $prefix
     */
    static function addQueue(array $params, $queue, $prefix = '')
    {
        $mq_conn = self::getConnection($queue);
        if($mq_conn) {
            if ($prefix) {
                $mq_conn->set_queue_name_space($prefix);
            }
            $params['mq_created_at'] = time();
            return $mq_conn->insert($params, $queue);
        }


    }


    static function queueItem($channel, $mobile, $group, $type)
    {
        //type=>17 是对应in项目中统计脚本 in/protected/commands/WorkersSysqueueCommand.php
        return $log_param = array('channel' => $channel, 'mobile' => $mobile, 'group' => $group, 'log_type' => $type, 'stat_type' => '','type'=>17);
    }

    /*
     * @param $item的值经过queueItem函数处理过
     * desc:将短信的发送情况 推送至队列用来统计失败/成功率
     */
    static function addSmsCodeInfoToQ($item)
    {
        //短息验证码 验证情况计数
        Log::debug(json_encode($item, JSON_UNESCAPED_UNICODE));
        //self::addQueue($item, "sysqueue");

    }

    /*
     * @param $item的值经过queueItem函数处理过
     * desc:将验证码的验证情况 推送至队列用来统计失败和成功率
     */
    static function addVerifyCodeInfoToQ($item)
    {
        //短息验证码 验证情况计数
        Log::debug(json_encode($item, JSON_UNESCAPED_UNICODE));
        //self::addQueue($item, "sysqueue");
    }

    /*
     * @param $item 数组
     *         mobile:手机号
     *         textKey:调用者类型(如:in)
     *         msg:deubg说明信息
     * desc:将详细的日志写入,方便调试和bug追踪
     */
    static function addSmsDebugLog($item)
    {
        $str = '';
        if($item['mobile'])
            $str.="UID:".$item['mobile'];
        if(isset($item['textKey']))
            $str .=" textKey:".$item['textKey'];
        $str .= " message:".$item['msg'];
        $item['content'] = $str;
        Log::debug(json_encode($item, JSON_UNESCAPED_UNICODE));
        //self::addQueue($item, "send_sms_log");
    }
}