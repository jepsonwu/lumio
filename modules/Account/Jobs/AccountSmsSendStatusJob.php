<?php

namespace Modules\Account\Jobs;

use Jiuyan\Common\Component\InFramework\Jobs\BaseJob;

class AccountSmsSendStatusJob extends BaseJob
{
    protected $_connection = 'rabbitMq1';
    protected $_queueName = 'in_sysqueue';

    public function __construct()
    {
        parent::__construct();
    }

    public function run()
    {
    }
}
