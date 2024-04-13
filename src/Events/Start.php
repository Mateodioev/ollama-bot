<?php

namespace Mateodioev\OllamaBot\Events;

use Mateodioev\Bots\Telegram\Types\User;
use Mateodioev\TgHandler\Commands\MessageCommand;

class Start extends MessageCommand
{
    protected string $name = 'start';
    protected string $description = 'Start the bot';
    protected array $prefix = ['/', '!', '.'];
    protected array $alias = ['help'];
    protected array $middlewares = [
        '\Mateodioev\OllamaBot\Events\Middlewares::authUser',
    ];

    public function execute(array $args = [])
    {
        $u = $this->ctx()->getUser();
        $this->api()->replyToMessage($this->ctx()->message, self::helpMessage($u));
    }

    public static function helpMessage(User $u): string
    {
        return "Hello " . $u->mention() . ", this is a bot that uses the ollama models.\nTo get an answer just send your question, you can change the model to use with the <code>/setmodel</code> command.\nTo view the available models use the <code>/models</code> command.";
    }
}
