<?php

declare(strict_types=1);

namespace ImageEngine\PhpSdk\Storage;

class RedisStorage implements StorageInterface
{
    private \Redis $redis;

    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    public function get(string $key)
    {
        return $this->redis->get($key);
    }

    public function set(string $key, string $value)
    {
        return $this->redis->set($key, $value);
    }

    public function delete(string $key)
    {
        return $this->redis->del($key);
    }
}
