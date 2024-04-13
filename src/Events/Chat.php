<?php

namespace Mateodioev\OllamaBot\Events;

use Mateodioev\TgHandler\Commands\MessageCommand;
use Mateodioev\TgHandler\Filters\{FilterNot, FilterPrivateChat};

#[FilterNot(new FilterPrivateChat())]
class Chat extends MessageCommand
{
    protected string $name = 'chat';
    protected array $prefix = ['/', '!', '.'];
    protected array $middlewares = [
        '\Mateodioev\OllamaBot\Events\Middlewares::authUser',
    ];

    public function execute(array $args = [])
    {
        $payload = $this->ctx()->getPayload();

        if (empty($payload)) {
            $this->onEmpty();
            return;
        }

        $user = $args[0];
        $streamCompletation = new OllamaStreamCompletation(
            $this->api(),
            $this->ctx(),
            $user,
            $this->logger()
        );

        $streamCompletation->run($payload);
    }

    private function onEmpty()
    {
        $this->api()->replyToMessage(
            $this->ctx()->message,
            'Please put some text'
        );
    }
}
