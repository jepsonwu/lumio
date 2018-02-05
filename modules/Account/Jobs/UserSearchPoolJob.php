<?php

namespace Modules\Account\Jobs;

use Jiuyan\Common\Component\InFramework\Jobs\BaseJob;

class UserSearchPoolJob extends BaseJob
{
    protected $_connection = 'rabbitMq1';
    protected $_queueName = 'in_solr_user';

    public function __construct()
    {
        parent::__construct();
    }

    public function run()
    {
    }
}
