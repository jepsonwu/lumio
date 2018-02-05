<?php

namespace Modules\Account\Jobs;

use Jiuyan\Common\Component\InFramework\Jobs\BaseJob;

class UserMobileChangeNoticeJob extends BaseJob
{
    protected $_connection = 'rabbitMq1';
    protected $_queueName = 'in_user_action_auth2';

    public function __construct()
    {
        parent::__construct();
    }

    public function run()
    {
    }
}
