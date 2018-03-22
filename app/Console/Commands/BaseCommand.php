<?php

namespace App\Console\Commands;

use App\Logger\Writer;
use Illuminate\Console\Command;
use Monolog\Logger;

//baseCommand Writer 移植到vendor
abstract class BaseCommand extends Command
{
    protected $logFileName = 'common';

    protected $logger;

    /**
     * @return Logger
     */
    protected function getLogger()
    {
        if (is_null($this->logger)) {
            $this->logger = new Logger('command');
            $writer = new Writer();
            $writer->setMonolog($this->logger);
            $writer->pushProcessor();
            $writer->useDailyFiles(storage_path() . "/logs/" . $this->logFileName . ".log", 30, 'info');
        }

        return $this->logger;
    }

    public function handle()
    {
        $this->getLogger()->info("start handle");
        call_user_func_array([$this, "executeHandle"], []);
        $this->getLogger()->info("done handle");
    }

    abstract protected function executeHandle();
}