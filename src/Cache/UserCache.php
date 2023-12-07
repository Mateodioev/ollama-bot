<?php

namespace Mateodioev\OllamaBot\Cache;

use Mateodioev\OllamaBot\Models\User;
use Mateodioev\OllamaBot\Repository\UserRepository;

class UserCache
{
    private const TTL = globalCache::TIME_HOUR;

    private static ?UserRepository $repo = null;

    public static function find(int $id): ?User
    {
        $cache = globalCache::get();
        $user  = $cache->get('user:' . $id) ?? self::$repo->find($id);

        if ($user !== null) {
            globalCache::get()->set('user:' . $id, $user, self::TTL);
            return $user;
        }

        return null;
    }

    public static function save(User $user): void
    {
        globalCache::get()->set('user:' . $user->id, $user, self::TTL);
        self::$repo->save($user);
    }

    public static function update(User $user): void
    {
        globalCache::get()->set('user:' . $user->id, $user, self::TTL);
        self::$repo->update($user);
    }

    public static function setRepo(UserRepository $repo): void
    {
        self::$repo = $repo;
    }
}
