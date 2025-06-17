<?php

namespace App\Utils;

class Money
{
    public static function toLAK($amount)
    {
        return number_format($amount, 0, '', ',');
    }

    public static function toInt($amount)
    {
        return (int) str_replace(',', '', $amount);
    }
}

