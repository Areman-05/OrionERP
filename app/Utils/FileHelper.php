<?php

namespace OrionERP\Utils;

class FileHelper
{
    public static function getExtension(string $filename): string
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    public static function isValidExtension(string $filename, array $allowedExtensions): bool
    {
        $extension = self::getExtension($filename);
        return in_array($extension, $allowedExtensions);
    }

    public static function sanitizeFilename(string $filename): string
    {
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        return $filename;
    }
}

