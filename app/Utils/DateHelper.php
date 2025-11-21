<?php

namespace OrionERP\Utils;

class DateHelper
{
    public static function now(): string
    {
        return date('Y-m-d H:i:s');
    }

    public static function format(string $date, string $format = 'Y-m-d'): string
    {
        return date($format, strtotime($date));
    }

    public static function isValid(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}

