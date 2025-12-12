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

    public static function first(array $array, callable $callback = null)
    {
        if ($callback === null) {
            return reset($array) ?: null;
        }

        foreach ($array as $item) {
            if ($callback($item)) {
                return $item;
            }
        }

        return null;
    }

    public static function last(array $array)
    {
        return end($array) ?: null;
    }

    public static function flatten(array $array, int $depth = 1): array
    {
        $result = [];
        foreach ($array as $item) {
            if (is_array($item) && $depth > 0) {
                $result = array_merge($result, self::flatten($item, $depth - 1));
            } else {
                $result[] = $item;
            }
        }
        return $result;
    }

    public static function unique(array $array, string $key = null): array
    {
        if ($key === null) {
            return array_unique($array, SORT_REGULAR);
        }

        $seen = [];
        $result = [];
        foreach ($array as $item) {
            $value = is_array($item) ? ($item[$key] ?? null) : null;
            if ($value !== null && !in_array($value, $seen)) {
                $seen[] = $value;
                $result[] = $item;
            }
        }
        return $result;
    }

    public static function sortBy(array $array, string $key, string $direction = 'asc'): array
    {
        usort($array, function($a, $b) use ($key, $direction) {
            $aValue = is_array($a) ? ($a[$key] ?? null) : null;
            $bValue = is_array($b) ? ($b[$key] ?? null) : null;
            
            if ($aValue == $bValue) return 0;
            
            $result = $aValue < $bValue ? -1 : 1;
            return $direction === 'desc' ? -$result : $result;
        });
        
        return $array;
    }

    public static function chunk(array $array, int $size): array
    {
        return array_chunk($array, $size);
    }

    public static function merge(array ...$arrays): array
    {
        return array_merge(...$arrays);
    }
}

