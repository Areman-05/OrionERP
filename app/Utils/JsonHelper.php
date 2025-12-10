<?php

namespace OrionERP\Utils;

class JsonHelper
{
    public static function encode($data, int $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES): string
    {
        $json = json_encode($data, $flags);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Error al codificar JSON: ' . json_last_error_msg());
        }
        
        return $json;
    }

    public static function decode(string $json, bool $assoc = true)
    {
        $data = json_decode($json, $assoc);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Error al decodificar JSON: ' . json_last_error_msg());
        }
        
        return $data;
    }

    public static function isValid(string $json): bool
    {
        json_decode($json);
        return json_last_error() === JSON_ERROR_NONE;
    }

    public static function prettyPrint($data): string
    {
        return self::encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}

