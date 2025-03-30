<?php

namespace Mateodioev\OllamaBot\Ollama;

use Amp\ByteStream\Payload;
use Amp\Cancellation;
use Amp\Http\Client\{HttpClientBuilder};
use Amp\Http\Client\Request;
use Exception;

use function json_encode;

class HttpClient
{
    public const BASE_URL = 'http://localhost:11434';
    public array $opts = [];

    public function __construct(
        private string $baseURL = self::BASE_URL,
        public string $model = 'llama2',
        public ?Cancellation $cancellation = null
    ) {
        $this->disableStream();
    }

    public function completion(string $prompt, ?OllamaParameters $options = null, array $context = []): Payload
    {
        return $this->makeRequest('/api/generate', [
            'model'   => $this->model,
            'prompt'  => $prompt,
            'options' => ($options ?? OllamaParameters::default())->toArray(),
            'context' => $context,
            ...$this->opts,
        ]);
    }

    /**
     * IGNORE
     */
    public function chat(Chat $messages, ?OllamaParameters $options = null): Payload
    {
        throw new Exception("Not implemented yet.");

        /* return $this->makeRequest('/api/chat', [
            'model'   => $this->model,
            'messages' => $messages->toJson(),
            'options' => ($options ?? OllamaParameters::default())->toArray(),
            ...$this->opts,
        ]); */
    }

    public function tags(): Payload
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

    private function makeRequest(string $endpoint, array $body, string $method = 'POST'): Payload
    {
        $client = HttpClientBuilder::buildDefault();

        foreach ($body as $key => $value) {
            if (empty($value) && is_bool($value) === false) {
                unset($body[$key]);
            }
        }

        $req = new Request(
            $this->baseURL . $endpoint,
            $method
        );
        if (!empty($body)) {
            $req->setBody(json_encode($body));
        }
        $req->setTransferTimeout(300);
        $req->setInactivityTimeout(70);

        $res = $client->request($req, $this->cancellation);

        return $res->getBody();
    }
}
