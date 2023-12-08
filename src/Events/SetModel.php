<?php

namespace Mateodioev\OllamaBot\Events;

use Mateodioev\Bots\Telegram\Api;
use Mateodioev\OllamaBot\Cache\UserCache;
use Mateodioev\OllamaBot\Models\User;
use Mateodioev\TgHandler\Commands\MessageCommand;
use Mateodioev\TgHandler\Context;

class SetModel extends MessageCommand
{
    protected string $name = 'setmodel';
    protected array $prefix = ['/', '!', '.'];
    protected array $middlewares = [
        '\Mateodioev\OllamaBot\Events\Middlewares::authUser',
    ];

    public function handle(Api $bot, Context $context, array $args = [])
    {
        /** @var User $user */
        $user    = $args[0];
        $payload = trim($context->getPayload());

        if (empty($payload)) {
            $this->onEmpty($user);
            return;
        }

        $user->model = $payload;
        UserCache::update($user);

        $this->api()->replyToMessage(
            $this->ctx()->message,
            'Model set to ' . $payload
        );
    }

    private function onEmpty(User $u)
    {
        $this->api()->replyToMessage(
            $this->ctx()->message,
            'Please specify a model to use'
              . "\nYou current model is: " . $u->model
        );
    }
}
