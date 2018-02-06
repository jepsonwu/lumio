<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/11/21
 * Time: 18:03
 */

namespace Jiuyan\Common\Component\InFramework\Components;

use Jiuyan\Common\Component\InFramework\Jobs\BaseJob;
use Log;

class CommonQueueDispatchComponent
{
    protected static $_instance;

    /**
     * @var BaseJob
     */
    private static $_queueJob = null;

    public static function getInstance($queueJob)
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance->_init($queueJob);
    }

    private function _init($queueJob)
    {
        try {
            self::$_queueJob = app($queueJob);
        } catch (\Exception $e) {
            Log::error('queue init failed');
        }
        return $this;
    }

    public function pushMsg($msgInfo)
    {
        if (!self::$_queueJob) {
            return false;
        }
        self::$_queueJob->setParams($msgInfo);
        dispatch(self::$_queueJob);
        return true;
    }
}