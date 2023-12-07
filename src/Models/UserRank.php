<?php

namespace Mateodioev\OllamaBot\Models;

enum UserRank: int
{
    case Banned = 1;
    case User = 2;
    case Admin = 3;
    case Owner = 4;

    /**
     * Check if the rank has the permission
     */
    public static function hasPermission(User $user, UserRank $permission): bool
    {
        return $user->rank >= $permission;
    }

    public static function try(int $id): UserRank
    {
        return UserRank::tryFrom($id) ?? UserRank::User;
    }
}
