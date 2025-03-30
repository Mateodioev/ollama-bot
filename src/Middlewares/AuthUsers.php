<?php

declare(strict_types=1);

namespace Mateodioev\OllamaBot\Middlewares;

use Mateodioev\Bots\Telegram\Api;
use Mateodioev\OllamaBot\Models\{User, UserRank};
use Mateodioev\TgHandler\Commands\StopCommand;
use Mateodioev\TgHandler\Context;
use Mateodioev\TgHandler\Middleware\Middleware;

class AuthUsers extends Middleware
{
    public function __invoke(Context $ctx, Api $api, array $args = [])
    {
        /** @var User $user */
        $user = $args[FindUserOrRegister::class];

        if ($user->rank === UserRank::Banned) {
            throw new StopCommand();
        }

        // Continue with the next middleware
    }
}
