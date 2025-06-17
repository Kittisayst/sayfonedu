<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class SysLog extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'system-log';
    }
}