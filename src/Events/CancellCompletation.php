<?php

namespace Mateodioev\OllamaBot\Events;

use Amp\DeferredCancellation;
use Mateodioev\Bots\Telegram\Api;
use Mateodioev\OllamaBot\Cache\globalCache;
use Mateodioev\TgHandler\Commands\{CallbackCommand};
use Mateodioev\TgHandler\Context;
use Throwable;

class CancellCompletation extends CallbackCommand
{
    protected string $name = 'cancel';

    public function handle(Api $bot, Context $context, array $args = [])
    {
        $cancelId = $context->getPayload();

        $deferredCancellation = globalCache::get()->get($cancelId);

        if ($deferredCancellation instanceof DeferredCancellation) {
            $this->cancel($deferredCancellation, $cancelId);
        }
    }

    private function cancel(DeferredCancellation $cancel, string $cancelId): void
    {
        globalCache::get()->delete($cancelId);

        try {
            $cancel->cancel();
        } catch (Throwable $e) {
            echo $e . PHP_EOL;
        } finally {
            $this->api()->answerCallbackQuery($this->ctx()->callbackQuery()->id, [
                'text'       => 'cancelled',
                'show_alert' => true
            ]);
        }
    }
}
