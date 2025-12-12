<?php

namespace OrionERP\Services;

class CacheService
{
    private $cacheDir;
    private $ttl;

    public function __construct(int $ttl = 3600)
    {
        $this->cacheDir = __DIR__ . '/../../cache/';
        $this->ttl = $ttl;
        
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    public function get(string $key): ?string
    {
        $file = $this->getCacheFile($key);
        
        if (!file_exists($file)) {
            return null;
        }
        
        $data = unserialize(file_get_contents($file));
        
        if (time() > $data['expires_at']) {
            unlink($file);
            return null;
        }
        
        return $data['value'];
    }

    public function set(string $key, string $value, ?int $ttl = null): void
    {
        $file = $this->getCacheFile($key);
        $ttl = $ttl ?? $this->ttl;
        
        $data = [
            'value' => $value,
            'expires_at' => time() + $ttl
        ];
        
        file_put_contents($file, serialize($data));
    }

    public function delete(string $key): void
    {
        $file = $this->getCacheFile($key);
        if (file_exists($file)) {
            unlink($file);
        }
    }

    public function clear(): void
    {
        $files = glob($this->cacheDir . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    private function getCacheFile(string $key): string
    {
        return $this->cacheDir . md5($key) . '.cache';
    }

    public function has(string $key): bool
    {
        $file = $this->getCacheFile($key);
        
        if (!file_exists($file)) {
            return false;
        }
        
        $data = unserialize(file_get_contents($file));
        
        if (time() > $data['expires_at']) {
            unlink($file);
            return false;
        }
        
        return true;
    }

    public function remember(string $key, callable $callback, ?int $ttl = null)
    {
        if ($this->has($key)) {
            return unserialize($this->get($key));
        }
        
        $value = $callback();
        $this->set($key, serialize($value), $ttl);
        
        return $value;
    }

    public function getStats(): array
    {
        $files = glob($this->cacheDir . '*.cache');
        $totalSize = 0;
        $expired = 0;
        
        foreach ($files as $file) {
            $totalSize += filesize($file);
            $data = unserialize(file_get_contents($file));
            if (time() > $data['expires_at']) {
                $expired++;
            }
        }
        
        return [
            'total_entries' => count($files),
            'expired_entries' => $expired,
            'total_size' => $totalSize,
            'total_size_mb' => round($totalSize / 1024 / 1024, 2)
        ];
    }
}
