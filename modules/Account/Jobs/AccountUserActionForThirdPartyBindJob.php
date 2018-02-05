<?php

namespace Modules\Account\Jobs;

use Jiuyan\Common\Component\InFramework\Jobs\BaseJob;

class AccountUserActionForThirdPartyBindJob extends BaseJob
{
    //队列连接名称
    protected $_connection = 'rabbitMq1';

    //队列名称
    protected $_queueName = 'in_user_action_toggle_bind';

    public function __construct()
    {
        //TODO:: 可以注入一些所需的服务实例
        parent::__construct();
    }

    public function run()
    {
        //TODO:: 编写业务处理逻辑
    }
}
