<?php

namespace App\Contracts;

interface CacheServiceInterface
{
    public function put(string $key, $value, $ttl = null);
    public function get(string $key, callable $callback, $ttl = null);
    public function forget(string $key);
    public function flush();
}
