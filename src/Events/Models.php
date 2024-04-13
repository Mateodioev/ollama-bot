<?php

declare(strict_types=1);

namespace Mateodioev\OllamaBot\Events;

use Mateodioev\OllamaBot\Ollama\HttpClient;
use Mateodioev\TgHandler\Commands\MessageCommand;

use function array_map;
use function explode;
use function json_decode;
use function Mateodioev\OllamaBot\env;
use function round;

/**
 * List the available models.
 */
class Models extends MessageCommand
{
    protected string $name = 'models';
    protected string $description = 'List the available models';
    protected array $prefix = ['/', '!', '.'];

    public function execute(array $args = [])
    {
        $tags    = $this->getTags();
        $message = $this->modelsMessage($tags);

        $this->api()->replyToMessage($this->ctx()->message(), $message);
    }

    private function modelsMessage(array $tags): string
    {
        $models = array_map(function (array $model) {
            [$name, $tag] = explode(':', $model['name']);
            $size         = (int) $model['size'];
            return "» <a href='https://ollama.com/library/$name'>$name:$tag</a> (" . static::readableSize($size) . ')';
        }, $tags['models']);

        return "<b>Available models:</b>\n\n" . join("\n", $models);
    }

    private function getTags(): array
    {
        $client = new HttpClient(baseURL: env('OLLAMA_HOST'));
        return json_decode($client->tags()->buffer(), true, flags: JSON_THROW_ON_ERROR);
    }

    public static function readableSize(int $bits): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $unit = 0;

        while ($bits >= 1024) {
            $bits /= 1024;
            $unit++;
        }

        return round($bits, 2) . ' ' . $units[$unit];
    }
}
