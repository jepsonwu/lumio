<?php

namespace Modules\Task\Commands;

use App\Console\Commands\BaseCommand;
use Modules\Task\Services\TaskOrderService;

class AutoOperateCommand extends BaseCommand
{
    protected $taskOrderService;

    protected $name = "TaskOrderAutoOperate";

    protected $description = "auto operate task order";

    protected $logFileName = "task_order_auto_operate";

    public function __construct(TaskOrderService $taskOrderService)
    {
        $this->taskOrderService = $taskOrderService;
        parent::__construct();
    }

    protected function executeHandle()
    {
        $this->taskOrderService->autoOperate($this->getLogger());
    }
}