<?php

namespace App\Logging;

use App\Facades\SysLog;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Monolog\LogRecord;

class DatabaseLogger
{
    /**
     * Create a custom Monolog instance.
     */
    public function __invoke(array $config)
    {
        $logger = new Logger('database');
        $logger->pushHandler(new DatabaseLoggerHandler());
        
        return $logger;
    }
}

class DatabaseLoggerHandler extends AbstractProcessingHandler
{
    protected function write(LogRecord $record): void
    {
        $level = strtolower($record->level->name);
        
        if ($level === 'notice' || $level === 'debug') {
            $level = 'info';
        } else if ($level === 'alert' || $level === 'emergency') {
            $level = 'critical';
        }
        
        SysLog::log(
            $level,
            $record->message,
            $record->channel,
            !empty($record->context) ? $record->context : null
        );
    }
}