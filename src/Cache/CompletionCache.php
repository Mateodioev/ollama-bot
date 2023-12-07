<?php

namespace Mateodioev\OllamaBot\Cache;

class CompletionCache
{
    public static function set(string $hash, array $data): void
    {
        globalCache::get()->set($hash, $data, globalCache::TIME_MINUTE * 30);
    }

    public static function getHash(string $hash): ?array
    {
        return globalCache::get()->get($hash);
    }
}
