<?php

namespace Mateodioev\OllamaBot\Events;

use Mateodioev\Bots\Telegram\Api;
use Mateodioev\OllamaBot\Cache\CompletionCache;
use Mateodioev\TgHandler\Commands\{CallbackCommand};
use Mateodioev\TgHandler\Context;

use function Amp\async;
use function Amp\Future\awaitAll;

class ViewCompletionDetails extends CallbackCommand
{
    private const NANOSECONDS = 1_000_000_000;

    protected string $name = 'completion_details';

    public function handle(Api $bot, Context $context, array $args = [])
    {
        $hash = $context->getPayload();
        $completion = CompletionCache::getHash($hash);

        if ($completion === null) {
            $this->logger()->debug('Cant find detail "{id}"', ['id' => $hash]);
            $this->notFoundCompletion();
            return;
        }

        $this->showCompletionDetails($completion);
    }

    private function notFoundCompletion()
    {
        $futures = [];
        $futures[] = async($this->api()->editMessageReplyMarkup(...), [
            'chat_id'    => $this->ctx()->getChatId(),
            'message_id' => $this->ctx()->getMessageId(),
        ]);
        $futures[] = async($this->api()->answerCallbackQuery(...), $this->ctx()->callbackQuery()->id, [
            'text'       => 'Not found',
            'show_alert' => true,
        ]);

        awaitAll($futures);
    }

    private function showCompletionDetails(array $completion)
    {
        $text = 'Model: ' . $completion['model']
            . "\nTotal duration: " . $this->nanoSecondsToSeconds($completion['total_duration']) . '\'s'
            . "\nLoad model duration: " . $this->nanoSecondsToSeconds($completion['load_duration']) . '\'s'
            . "\nToken/S: " . round($completion['eval_count'] / $completion['eval_duration'], 3);

        $this->api()->answerCallbackQuery($this->ctx()->callbackQuery()->id, [
            'text' => $text,
            'show_alert' => false,
        ]);
    }

    private function nanoSecondsToSeconds(int $nanoseconds, int $round = 2): float
    {
        return round($nanoseconds / self::NANOSECONDS, $round);
    }
}
