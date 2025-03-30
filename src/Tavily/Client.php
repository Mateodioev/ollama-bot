<?php

declare(strict_types=1);

namespace Mateodioev\OllamaBot\Tavily;

use Amp\Http\Client\{HttpClientBuilder, Request};

class Client
{
    public const string BASE_URL = 'https://api.tavily.com/search';

    public function __construct(private string $apiKey)
    {
        # code...
    }

    public function httpClient(array $params)
    {
        $client = HttpClientBuilder::buildDefault();

        $request = new Request(self::BASE_URL, 'POST');
        $request->addHeader('Content-Type', 'application/json');
        $request->addHeader('Authorization', "Bearer {$this->apiKey}");

        $request->setBody(json_encode($params));

        $response = $client->request($request);

        return $response->getBody()->buffer();
    }

    public function search(string $query, ?SearchSettings $settings = null): Response
    {
        $settings ??= new SearchSettings();
        $params   = $settings->build($query);

        $result = $this->httpClient($params);

        return Response::map(json_decode($result, true));
    }
}
