<?php

namespace Mateodioev\OllamaBot\Events;

use Mateodioev\OllamaBot\Middlewares\{AuthUsers, FindUserOrRegister};
use Mateodioev\OllamaBot\Models\User;
use Mateodioev\TgHandler\Events\Types\MessageEvent;
use Mateodioev\TgHandler\Filters\FilterPrivateChat;

#[FilterPrivateChat]
class PrivateTextListener extends MessageEvent
{
    protected array $middlewares = [
        FindUserOrRegister::class,
        AuthUsers::class,
    ];

    public function execute(array $args = [])
    {
        /** @var User $user */
        $user   = $args[FindUserOrRegister::class];
        $stream = new OllamaStreamCompletion(
            $this->api(),
            $this->ctx(),
            $user,
            $this->logger()
        );

        $stream->run($this->ctx()->message->text);
    }

    public function isValid(): bool
    {
        $text = $this->ctx()->message->text;
        $cmd  = $text[0] ?? '';

        // Make sure it is not a command
        return parent::isValid()
            && empty($text) === false
            && in_array($cmd, ['/', '!', '.']) === false;
    }
}
