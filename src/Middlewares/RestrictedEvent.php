<?php

declare(strict_types=1);

namespace Mateodioev\OllamaBot\Middlewares;

use Mateodioev\Bots\Telegram\Api;
use Mateodioev\OllamaBot\Models\{User, UserRank};
use Mateodioev\TgHandler\Commands\StopCommand;
use Mateodioev\TgHandler\Context;
use Mateodioev\TgHandler\Middleware\Middleware;

abstract class RestrictedEvent extends Middleware
{
    private UserRank $rank;

    public function __construct(UserRank|int $rank)
    {
        if (!$rank instanceof UserRank) {
            $rank = UserRank::from($rank);
        }

        $this->rank = $rank;
    }

    public function __invoke(Context $ctx, Api $api, array $args = [])
    {
        /** @var User $user */
        $user = $args[FindUserOrRegister::class];

        if (UserRank::hasPermission($user, $this->rank)) {
            return true;
        }

        $this->onInvalidRol($user, $ctx, $api, $args);
        throw new StopCommand();
    }

    abstract protected function onInvalidRol(User $user, Context $ctx, Api $api, array $args = []): void;
}
