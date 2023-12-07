<?php

namespace Mateodioev\OllamaBot\Events;

use Mateodioev\Bots\Telegram\Api;
use Mateodioev\OllamaBot\Cache\UserCache;
use Mateodioev\OllamaBot\Models\{OllamaModels, User, UserRank};
use Mateodioev\TgHandler\Commands\StopCommand;
use Mateodioev\TgHandler\Context;

class Middlewares
{
    public const DEFAULT_MODEL = 'codellama';

    public static function registerUser(Context $ctx, Api $bot): User
    {
        $user = UserCache::find($ctx->getUserId());

        if ($user === null) {
            $user = new User(
                id: $ctx->getUserId(),
                model: self::DEFAULT_MODEL,
                rank: UserRank::User->value
            );
            UserCache::save($user);
        }

        return $user;
    }

    public static function authUser(Context $ctx, Api $bot): User
    {
        $user = self::registerUser($ctx, $bot);

        if ($user->rank === UserRank::Banned) {
            throw new StopCommand('<b>You are banned</b>');
        }

        return $user;
    }

    public static function silentAuthUser(Context $ctx, Api $bot): User
    {
        $user = self::registerUser($ctx, $bot);

        if ($user->rank === UserRank::Banned) {
            throw new StopCommand();
        }

        return $user;
    }
}
