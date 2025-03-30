<?php

namespace Mateodioev\OllamaBot\Events;

use Amp\DeferredCancellation;
use Mateodioev\OllamaBot\Cache\globalCache;
use Mateodioev\TgHandler\Commands\{CallbackCommand};
use Throwable;

class TerminateCompletionRequest extends CallbackCommand
{
    protected string $name = 'cancel';

    public function execute(array $args = [])
    {
        $cancelId = $this->ctx()->getPayload();

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
                'show_alert' => true,
            ]);
        }
    }
}
