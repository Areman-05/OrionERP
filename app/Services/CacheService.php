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
}
