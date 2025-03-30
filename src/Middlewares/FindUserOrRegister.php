<?php

declare(strict_types=1);

namespace Mateodioev\OllamaBot\Middlewares;

use Mateodioev\Bots\Telegram\Api;
use Mateodioev\Bots\Telegram\Types\User as TgUser;
use Mateodioev\OllamaBot\Cache\UserCache;
use Mateodioev\OllamaBot\Models\{User, UserRank};
use Mateodioev\TgHandler\Commands\StopCommand;
use Mateodioev\TgHandler\Context;
use Mateodioev\TgHandler\Middleware\Middleware;

class FindUserOrRegister extends Middleware
{
    public const string DEFAULT_MODEL = 'deepseek-r1:8b';

    public function __invoke(Context $ctx, Api $api, array $args = [])
    {
        return self::find($ctx, $api);
    }

    public static function find(Context $ctx, Api $api): ?User
    {
        $tgUser = $ctx->getUser() ?? throw new StopCommand('User not found in context');

        $user = UserCache::find($tgUser->id());
        return $user ?? self::registerUser($tgUser, $api, $ctx);
    }

    private static function registerUser(TgUser $tgUser, Api $api, Context $ctx): User
    {
        $user = new User(
            id: $tgUser->id(),
            model: self::DEFAULT_MODEL,
            rank: UserRank::User->value,
        );
        UserCache::save($user);
        return $user;
    }
}
