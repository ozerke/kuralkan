<?php

namespace App\Services;

use App\Contracts\CacheServiceInterface;
use Illuminate\Support\Facades\Cache;

class CacheService implements CacheServiceInterface
{
    const ONE_MINUTE = 60;
    const FIVE_MINUTES = self::ONE_MINUTE * 5;
    const TEN_MINUTES = self::ONE_MINUTE * 10;
    const ONE_HOUR = self::ONE_MINUTE * 60;
    const ONE_DAY = self::ONE_HOUR * 24;

    public function put(string $key, $value, $ttl = self::FIVE_MINUTES)
    {
        Cache::put($key, $value, $ttl);
    }

    public function get(string $key, callable $callback, $ttl = self::FIVE_MINUTES)
    {
        return Cache::remember($key, $ttl, $callback);
    }

    public function forget(string $key)
    {
        Cache::forget($key);
    }

    public function flush()
    {
        Cache::flush();
    }
}
