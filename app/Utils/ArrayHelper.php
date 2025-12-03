<?php

namespace OrionERP\Utils;

class ArrayHelper
{
    public static function pluck(array $array, string $key): array
    {
        return array_map(function($item) use ($key) {
            return is_array($item) ? ($item[$key] ?? null) : null;
        }, $array);
    }

    public static function groupBy(array $array, string $key): array
    {
        $grouped = [];
        
        foreach ($array as $item) {
            $groupKey = is_array($item) ? ($item[$key] ?? null) : null;
            if ($groupKey !== null) {
                if (!isset($grouped[$groupKey])) {
                    $grouped[$groupKey] = [];
                }
                $grouped[$groupKey][] = $item;
            }
        }
        
        return $grouped;
    }

    public static function keyBy(array $array, string $key): array
    {
        $keyed = [];
        
        foreach ($array as $item) {
            if (is_array($item) && isset($item[$key])) {
                $keyed[$item[$key]] = $item;
            }
        }
        
        return $keyed;
    }

    public static function where(array $array, callable $callback): array
    {
        return array_filter($array, $callback);
    }
}

