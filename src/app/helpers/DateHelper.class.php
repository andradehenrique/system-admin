<?php

namespace App\Helpers;

class DateHelper
{
    const PT_BR_DATE = 'd/m/Y';
    const PT_BR_DATETIME = 'd/m/Y H:i:s';
    const PT_BR_DATE_HOUR_MINUTE = 'd/m/Y H:i';

    public static function formatDate($date, $format = self::PT_BR_DATE_HOUR_MINUTE): mixed
    {
        $date = new \DateTime($date);
        return $date->format($format);
    }
}
