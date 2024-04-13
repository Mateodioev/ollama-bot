<?php

namespace Mateodioev\OllamaBot\Events;

use Mateodioev\OllamaBot\Cache\CompletionCache;
use Mateodioev\TgHandler\Commands\{CallbackCommand};

use function Amp\async;
use function Amp\Future\awaitAll;
use function json_encode;
use function round;

class ViewCompletionDetails extends CallbackCommand
{
    private const NANOSECONDS = 1_000_000_000;

    protected string $name = 'completion_details';

    public function execute(array $args = [])
    {
        $hash = $this->ctx()->getPayload();
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
        $this->logger()->debug('Show completion details for: {completion}', ['completion' => json_encode($completion)]);

        $text = 'Model: ' . $completion['model']
            . "\nTotal duration: " . static::readableNanoSeconds($completion['total_duration']) . '\'s'
            . "\nLoad model duration: " . static::readableNanoSeconds($completion['load_duration']) . '\'s'
            // I'm not sure if this is the right way to calculate the token/s
            . "\nToken/s: " . round($completion['eval_count'] / $this->nanoSecondsToSeconds($completion['eval_duration']), 2);

        $this->api()->answerCallbackQuery($this->ctx()->callbackQuery()->id, [
            'text' => $text,
            'show_alert' => true,
        ]);
    }

    private function nanoSecondsToSeconds(int $nanoseconds): float
    {
        return $nanoseconds / self::NANOSECONDS;
    }

    public static function readableNanoSeconds(int $nanoseconds)
    {
        $units = ['s' => 1000000000, 'ms' => 1000000, 'μs' => 1000, 'ns' => 1];

        foreach ($units as $unit => $divisor) {
            $value = $nanoseconds / $divisor;
            if ($value >= 1) {
                return round($value, 2) . ' ' . $unit;
            }
        }

        return $nanoseconds . ' ns';
    }
}
