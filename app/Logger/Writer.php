<?php

namespace App\Logger;

use Request;
use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;

class Writer
{
    /**
     * The Monolog logger instance.
     *
     * @var \Monolog\Logger
     */
    protected $monolog;

    public function setMonolog($monolog)
    {
        $this->monolog = $monolog;
    }

    public function pushProcessor()
    {
        $this->monolog->pushProcessor(
            function (array $record) {
                $record['now'] = substr($record['datetime']->format('Y-m-d H:i:s.u'), 0, -3);
                $record['pid'] = getmypid();
                $record['session_id'] = session_id() ?: 'null';
                $record['ip'] = Request::ip();
                $record['request_id'] = Request::header("request_id") ?: '-';
                $record['caller'] = Request::header("caller") ?: '-';

                return $record;
            }
        );
    }

    public function useFiles($path, $level = 'debug')
    {
        $level = $this->parseLevel($level);

        $this->monolog->pushHandler($handler = new StreamHandler($path, $level));

        $handler->setFormatter($this->getDefaultFormatter());
    }

    public function useDailyFiles($path, $days = 0, $level = 'debug')
    {
        $level = $this->parseLevel($level);

        $this->monolog->pushHandler($handler = new RotatingFileHandler($path, $days, $level));

        $handler->setFormatter($this->getDefaultFormatter());
    }

    protected function getDefaultFormatter()
    {
        return new LineFormatter("%now% %level_name% %channel% [%pid%] [%request_id%] %ip% %caller%  ## %message% %context%\n", "Y-m-d H:i:s.u", true);
    }

    protected function parseLevel($level)
    {
        switch ($level) {
            case 'debug':
                return MonologLogger::DEBUG;
            case 'info':
                return MonologLogger::INFO;
            case 'notice':
                return MonologLogger::NOTICE;
            case 'warning':
                return MonologLogger::WARNING;
            case 'error':
                return MonologLogger::ERROR;
            case 'critical':
                return MonologLogger::CRITICAL;
            case 'alert':
                return MonologLogger::ALERT;
            case 'emergency':
                return MonologLogger::EMERGENCY;
            default:
                throw new \InvalidArgumentException("Invalid log level.");
        }
    }
}
