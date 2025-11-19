<?php

namespace OrionERP\Utils;

class Validator
{
    public static function email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function required($value): bool
    {
        return !empty($value);
    }

    public static function minLength(string $value, int $min): bool
    {
        return strlen($value) >= $min;
    }

    public static function maxLength(string $value, int $max): bool
    {
        return strlen($value) <= $max;
    }
}

