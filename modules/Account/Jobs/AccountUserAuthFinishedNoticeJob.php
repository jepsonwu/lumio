<?php

namespace Modules\Account\Jobs;

use Jiuyan\Common\Component\InFramework\Jobs\BaseJob;

class AccountUserAuthFinishedNoticeJob extends BaseJob
{
    //队列连接名称
    protected $_connection = 'rabbitMq1';

    //队列名称
    protected $_queueName = 'in_user_action_auth2';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function run()
    {
        //TODO:: 编写业务处理逻辑
    }
}
