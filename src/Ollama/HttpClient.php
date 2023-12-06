<?php

namespace Mateodioev\OllamaBot\Ollama;

use Exception;
use Mateodioev\Bots\Telegram\Http\{AsyncClient, Methods};

use function json_decode;
use function json_encode;

class HttpClient
{
    public const BASE_URL = 'http://localhost:11434';
    public array $opts = [];

    public function __construct(
        private string $baseURL = self::BASE_URL,
        public string $model = 'llama2'
    ) {
        $this->disableStream();
    }

    public function completion(string $prompt, ?OllamaParameters $options = null, array $context = []): array
    {
        return $this->makeRequest('/api/generate', [
            'model'   => $this->model,
            'prompt'  => $prompt,
            'options' => ($options ?? OllamaParameters::default())->toArray(),
            'context' => $context,
            ...$this->opts,
        ]);
    }

    public function chat(Chat $messages, ?OllamaParameters $options = null): array
    {
        throw new Exception("Not implemented yet.");

        /* return $this->makeRequest('/api/chat', [
            'model'   => $this->model,
            'messages' => $messages->toJson(),
            'options' => ($options ?? OllamaParameters::default())->toArray(),
            ...$this->opts,
        ]); */
    }

    public function tags(): array
    {
        return $this->makeRequest('/api/tags', [], 'GET');
    }

    public function disableStream(): static
    {
        return $this->setStream(false);
    }

    /**
     * When is enabled, the response is returned as a stream of objects.
     */
    public function enableStream(): static
    {
        return $this->setStream(true);
    }

    private function setStream(bool $stream): static
    {
        $this->opts['stream'] = $stream;
        return $this;
    }

    private function makeRequest(string $endpoint, array $body, string $method = 'POST'): array
    {
        foreach ($body as $key => $value) {
            if (empty($value) && is_bool($value) === false) {
                unset($body[$key]);
            }
        }

        $client = new AsyncClient();
        if ($method === 'POST') {
            $client->new($this->baseURL . $endpoint, json_encode($body), Methods::POST);
        } else {
            $client->new($this->baseURL . $endpoint, json_encode($body), Methods::GET);
        }
        $res  = $client->setTimeout(100)->run();
        $body = $res->getBody();

        return json_decode($body, true, flags: JSON_THROW_ON_ERROR);
    }
}
