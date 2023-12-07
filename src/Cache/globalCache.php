<?php

namespace Mateodioev\OllamaBot\Cache;

use Amp\Cache\{Cache, LocalCache};

class globalCache
{
    public const TIME_SECOND = 1,
    TIME_MINUTE = 60,
    TIME_HOUR   = 3600,
    TIME_DAY    = 86400;

    private static ?Cache $cache = null;

    public static function get(): Cache
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        return (self::$cache = new LocalCache(gcInterval: self::TIME_HOUR));
    }
}
