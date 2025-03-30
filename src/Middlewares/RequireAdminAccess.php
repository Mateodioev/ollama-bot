<?php

declare(strict_types=1);

namespace Mateodioev\OllamaBot\Middlewares;

use Mateodioev\Bots\Telegram\Api;
use Mateodioev\OllamaBot\Models\{User, UserRank};
use Mateodioev\TgHandler\Context;

class RequireAdminAccess extends RestrictedEvent
{
    public function __construct()
    {
        parent::__construct(UserRank::Admin);
    }

    protected function onInvalidRol(User $user, Context $ctx, Api $api, array $args = []): void
    {
        $this->logger()->info("Unauthorized user {$user->id} tried to access an admin-only command");
        $this->logger()->debug('{user}', ['user' => (string) $user]);
    }
}
