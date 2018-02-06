<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/11/21
 * Time: 17:52
 */

namespace Jiuyan\Common\Component\InFramework\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

abstract class BaseJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $_connection = 'rabbitmq';
    protected $_queueName = '';

    public $params = [];

    public function __construct()
    {
        if ($this->_queueName) {
            $this->onQueue($this->_queueName);
        }
        if ($this->_connection) {
            $this->onConnection($this->_connection);
        }
    }

      /**
     * @param Job $job
     * @param array $params
     */
    public function handle($job = null, $params = [])
    {
        $this->setParams($params);
        $this->run();
        if (! $job->isDeletedOrReleased()) {
            $job->delete();
        }
    }

    abstract public function run();


    public function setParams($params = [])
    {
        if (!isset($params['timestamp']) || !$params['timestamp']) {
            $params['timestamp'] = time();
        }
        $this->params = $params;
    }

    /**
     * @param Queue $queue
     */
    public function queue(Queue $queue)
    {
        $payload = array_merge(
            $this->params,
            [
                'create_time' => time(),
                'from' => 'message',
                'job' => get_class($this) . '@handle',
                'data' => $this->params,
            ]
        );
        $queue->pushRaw(json_encode($payload), $this->queue);
    }


    public function errorLog($errorMsg)
    {
        Log::error('job err log ' . $this->_queueName . ' :: ' . $errorMsg);
    }

    public function infoLog($infoMsg)
    {
        Log::info('job info log ' . $this->_queueName . ' :: ' . $infoMsg);
    }
}
