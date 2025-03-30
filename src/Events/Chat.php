<?php

namespace Mateodioev\OllamaBot\Events;

use Mateodioev\OllamaBot\Middlewares\FindUserOrRegister;
use Mateodioev\TgHandler\Commands\MessageCommand;
use Mateodioev\TgHandler\Filters\{FilterNot, FilterPrivateChat};

#[FilterNot(new FilterPrivateChat())]
class Chat extends MessageCommand
{
    protected string $name        = 'chat';
    protected array  $prefix      = ['/', '!', '.'];
    protected array  $middlewares = [
        FindUserOrRegister::class,
    ];

    public function execute(array $args = [])
    {
        $payload = $this->ctx()->getPayload();

        if (empty($payload)) {
            $this->onEmpty();
            return;
        }

        $user   = $args[FindUserOrRegister::class];
        $stream = new OllamaStreamCompletion(
            $this->api(),
            $this->ctx(),
            $user,
            $this->logger()
        );

        $stream->run($payload);
    }

    private function onEmpty()
    {
        $this->api()->replyToMessage(
            $this->ctx()->message,
            'Please put some text'
        );
    }
}
