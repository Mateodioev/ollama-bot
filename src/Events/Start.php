<?php

namespace Mateodioev\OllamaBot\Events;

use Mateodioev\Bots\Telegram\Api;
use Mateodioev\Bots\Telegram\Types\User;
use Mateodioev\TgHandler\Commands\MessageCommand;
use Mateodioev\TgHandler\Context;

class Start extends MessageCommand
{
    protected string $name = 'start';
    protected string $description = 'Start the bot';
    protected array $prefix = ['/', '!', '.'];
    protected array $alias = ['help'];
    protected array $middlewares = [
        '\Mateodioev\OllamaBot\Events\Middlewares::authUser',
    ];

    public function handle(Api $bot, Context $context, array $args = [])
    {
        $u = $context->getUser();
        $bot->replyToMessage($context->message, self::helpMessage($u));
    }

    public static function helpMessage(User $u): string
    {
        return "Hello " . $u->mention() . ", this is a bot that uses the ollama models.\nTo get an answer just send your question, you can change the model to use with the <code>/setmodel</code> command." .
            "\n\n<b>Available models:</b>\n" .
            "\n- codellama (<b>default</b>)\n- codellama-custom \n- llama2-uncensored\n- wizard-math";
    }
}
