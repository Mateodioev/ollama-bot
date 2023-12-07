<?php

namespace Mateodioev\OllamaBot\Events;

use Mateodioev\OllamaBot\Models\User;
use Mateodioev\TgHandler\Events\Types\MessageEvent;
use Mateodioev\TgHandler\Filters\FilterPrivateChat;

#[FilterPrivateChat]
class PrivateTextListener extends MessageEvent
{
    protected array $middlewares = [
        '\Mateodioev\OllamaBot\Events\Middlewares::silentAuthUser',
    ];

    public function execute(array $args = [])
    {
        /** @var User $user */
        $user       = $args[0];
        $streamCompletation = new OllamaStreamCompletation(
            $this->api(),
            $this->ctx(),
            $user,
            $this->logger()
        );

        $streamCompletation->run($this->ctx()->message->text);
    }

    public function isValid(): bool
    {
        $text = $this->ctx()->message->text;
        $cmd = $text[0] ?? '';

        // Make sure it is not a command
        return parent::isValid()
            && empty($text) === false
            && in_array($cmd, ['/', '!', '.']) === false;
    }
}
