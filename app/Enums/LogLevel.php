<?php

// ສ້າງໄຟລ໌ໃໝ່: app/Enums/LogLevel.php
namespace App\Enums;

enum LogLevel: string
{
    case INFO = 'info';
    case WARNING = 'warning';
    case ERROR = 'error';
    case CRITICAL = 'critical';
}