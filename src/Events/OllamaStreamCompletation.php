<?php

namespace Mateodioev\OllamaBot\Events;

use Amp\ByteStream\Payload;
use Amp\Http\Client\SocketException;
use Amp\{CancelledException, DeferredCancellation};
use Exception;
use Mateodioev\Bots\Telegram\Api;
use Mateodioev\Bots\Telegram\Buttons\ButtonFactory;
use Mateodioev\Bots\Telegram\Config\strUtils;
use Mateodioev\Bots\Telegram\Exception\TelegramApiException;
use Mateodioev\Bots\Telegram\Types\Message;
use Mateodioev\OllamaBot\Cache\{CompletionCache, UserCache, globalCache};
use Mateodioev\OllamaBot\Models\User;
use Mateodioev\OllamaBot\Ollama\HttpClient;
use Mateodioev\TgHandler\Context;
use Mateodioev\TgHandler\Log\Logger;
use Revolt\EventLoop;

use Throwable;

use function array_rand;
use function json_decode;
use function Mateodioev\OllamaBot\env;
use function rand;
use function spl_object_id;

final class OllamaStreamCompletation
{
    private const SPLIT_TOKEN = 27;

    private HttpClient $client;
    private DeferredCancellation $deferredCancellation;
    private string $cancelToken;

    public function __construct(
        private Api $bot,
        private Context $ctx,
        public User $user,
        private ?Logger $logger = null
    ) {
        $this->deferredCancellation = new DeferredCancellation();
        globalCache::get()->set(
            $this->cancelToken = spl_object_id($this->deferredCancellation),
            $this->deferredCancellation
        );

        $this->client = new HttpClient(
            baseURL: env('OLLAMA_HOST'),
            model: $this->toSnakeCase($this->user->model),
            cancellation: $this->deferredCancellation->getCancellation()
        );
        $this->client->enableStream();
    }

    public function run(string $payload)
    {
        $this->logger->debug('Generating "{model}" completion for text: {txt}', [
            'model' => $this->client->model,
            'txt' => $payload
        ]);

        $message  = $this->bot->replyToMessage($this->ctx->message, 'Please wait...');
        $cancelId = $this->futureEditRandomValues($message);

        try {
            $body = $this->client->completion($payload, context: $this->user->lastContext);
            EventLoop::cancel($cancelId);
            $this->streamBody($body, $message);
        } catch (CancelledException | SocketException) {
            $this->bot->editMessageReplyMarkup([
                'chat_id'    => $message->chat->id,
                'message_id' => $message->message_id
            ]);
        } catch (Exception $e) {
            echo $e::class . '-' . $e . PHP_EOL;
            $this->ollamaError(['error' => 'Fail to run ollama, please try again']);
        }
    }

    private function streamBody(Payload $body, Message $message)
    {
        $i    = 0;
        $text = '';
        $txt  = []; // For use out of the while

        $params = [
            'message_id'   => $message->message_id,
            'parse_mode'   => 'html',
            'reply_markup' => (string) ButtonFactory::inlineKeyboardMarkup()->addCeil([
                'text'          => 'Cancel ❌',
                'callback_data' => 'cancel ' . $this->cancelToken
            ])
        ];

        while (null !== $content = $body->read()) {
            $this->logger->debug('Ollama completion response: {response}', [
                'response' => \trim($content)
            ]);

            $i++;

            $txt = json_decode(trim($content), true);
            if (isset($txt['error'])) {
                $this->ollamaError($txt);
                $body->close();
                $this->deferredCancellation->cancel();
                return;
            }

            $text .= $txt['response'];

            if ($i % self::SPLIT_TOKEN === 0 && $txt['done'] === false) {
                $this->bot->editMessageText(
                    $message->chat->id,
                    strUtils::scapeHtmlTags($text),
                    $params
                );
            }
        }

        if (empty($txt)) {
            $this->ollamaError(['error' => 'Empty response']);
            return;
        }

        $this->user->lastContext = $txt['context'] ?? [];
        UserCache::update($this->user);

        try {
            $this->bot->editMessageText(
                $message->chat->id,
                $text,
                [
                    'message_id' => $message->message_id,
                    'parse_mode' => 'markdown',
                    'reply_markup' => (string) ButtonFactory::inlineKeyboardMarkup()->addCeil([
                        'text' => 'Details 📝',
                        'callback_data' => 'completion_details ' . $this->cacheResponse($txt)
                    ])
                ]
            );
        } catch (TelegramApiException) {
            $this->bot->editMessageText(
                $message->chat->id,
                strUtils::scapeHtmlTags($text),
                [
                    'message_id' => $message->message_id,
                    'parse_mode' => 'html',
                    'reply_markup' => (string) ButtonFactory::inlineKeyboardMarkup()->addCeil([
                        'text' => 'Details 📝',
                        'callback_data' => 'completion_details ' . $this->cacheResponse($txt)
                    ])
                ]
            );
        }
    }

    private function ollamaError(array $err)
    {
        try {
            $this->bot->editMessageText(
                $this->ctx->message->chat->id,
                $err['error'],
                ['message_id' => $this->ctx->message->message_id]
            );
        } catch (Throwable $th) {
            $this->logger->error($th->getMessage());
            $this->bot->sendMessage(
                $this->ctx->message->chat->id,
                $err['error']
            );
        }
    }

    /**
     * @return string Cancel id
     */
    private function futureEditRandomValues(Message $message): string
    {
        $values = [
            'Please wait...',
            'I\'m thinking... 🤔',
            'let\'s go for a coffee ☕️',
            'Oops, it\'s taking longer than I thought.'
        ];

        $params = [
            'message_id'   => $message->message_id,
            'parse_mode'   => 'markdown',
            'reply_markup' => (string) ButtonFactory::inlineKeyboardMarkup()->addCeil([
                'text'          => 'Cancel ❌',
                'callback_data' => 'cancel ' . $this->cancelToken
            ])
        ];

        return EventLoop::repeat(rand(10, 30), function () use ($message, $values, $params) {
            $value = $values[array_rand($values)];
            $this->bot->editMessageText(
                $message->chat->id,
                $value,
                $params
            );
        });
    }

    private function cacheResponse(array $response): string
    {
        $hash = md5($response['response'] . $response['created_at']);
        CompletionCache::set($hash, $response);

        return $hash;
    }

    private function toSnakeCase(string $str): string
    {
        return strUtils::toSnakeCase($str);
    }
}
