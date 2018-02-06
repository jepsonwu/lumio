<?php

/**
 * 基于RabbitMQ的系统任务队列Service
 * @author xinghuo
 * @date   2012-10-11
 */
namespace  In\Sms\mq;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
class MQProducer {
    public $queue_name_space = '';
    private $con = '';
    private $ch ;
    private $connect_timeout = false;
    function __construct($host, $port, $user, $password, $vhost = "/", $debug = false, $insist = false, $login_method = "AMQPLAIN", $login_response = null, $locale = "en_US", $connection_timeout = 3000, $read_write_timeout = 300, $context = null) {
        /*define ( 'HOST', $host );
		define ( 'PORT', $port );
		define ( 'USER', $user );
		define ( 'PASS', $password );
		define ( 'VHOST', $vhost );*/
        //If this is enabled you can see AMQP output on the CLI
        //define ( 'AMQP_DEBUG', $debug );
        try {
            $this->con = new AMQPStreamConnection($host, $port, $user, $password, $vhost, $insist, $login_method, $login_response, $locale, $connection_timeout, $read_write_timeout, $context);
        } catch (Exception $e) {
            $this->connect_timeout = true;
           /* Yii::log("rabbitmq AMQPConnection Fail!".$e->getMessage(),CLogger::LEVEL_ERROR);
            ob_start();
            debug_print_backtrace ();
            $trace = ob_get_contents();
            ob_clean();
            Yii::log("rabbitmq AMQPConnection Fail INFO!".$trace,CLogger::LEVEL_ERROR);*/

        }


    }
    public function insert(array $params, $queue, $exchange = null) {
        if (!$this->connect_timeout) {
            $this->_insert( $params, $queue,  $exchange);
            $this->close_ch();//add calling close channel， added by xiatian at 2014-12-08
        }else{
            //Yii::log("rabbitmq connect timeout!".' queue:'.$queue.',json:'.json_encode($params),CLogger::LEVEL_ERROR);
        }
    }
    /**
     * 插入一条新的系统任务队列
     * @param array $fields
     * @param array $fields
     * @param string $queue
     * @param $exchange $queue
     * @return int $id;
     */
    public function _insert(array $params, $queue, $exchange = null) {
        try {
            $msg_body = json_encode ( $params );
            $queue = strtolower( $this->queue_name_space . $queue);
            $exchange = $exchange === null ? $queue : $exchange;

            $this->ch = $this->con->channel ();

            /*
            The following code is the same both in the consumer and the producer.
            In this way we are sure we always have a queue to consume from and an
                exchange where to publish messages.
           */

            /*
            name: $queue
            passive: false
            durable: true // the queue will survive server restarts
            exclusive: false // the queue can be accessed in other channels
            auto_delete: false //the queue won't be deleted once the channel is closed.
        */
            $this->ch->queue_declare ( $queue, false, true, false, false );
            /*
            name: $exchange
            type: direct
            passive: false
            durable: true // the exchange will survive server restarts
            auto_delete: false //the exchange won't be deleted once the channel is closed.
        */

            $this->ch->exchange_declare ( $exchange, 'direct', false, true, false );

            $this->ch->queue_bind ( $queue, $exchange );
            $msg = new AMQPMessage ( $msg_body, array ('content_type' => 'text/plain', 'delivery_mode' => 2 ) );

            $d = $this->ch->basic_publish ( $msg, $exchange );

        } catch (Exception $e) {
            return false;
        }



    }

    public function count($queue){
        $queue = strtolower( $this->queue_name_space . $queue);
        $this->ch = $this->con->channel ();
        $num=$this->ch->queue_declare ( $queue, false, true, false, false );
        return $num;
    }

    /**
     * 关闭channel
     */
    public function close_ch(){
        $this->ch->close ();
    }

    function close() {
        $this->ch->close ();
        $this->con->close ();
    }
    function set_queue_name_space($name) {
        if ($name)
            $this->queue_name_space = $name . '_';
    }
}