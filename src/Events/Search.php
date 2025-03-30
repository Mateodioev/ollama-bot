<?php

declare(strict_types=1);

namespace Mateodioev\OllamaBot\Events;

use Mateodioev\OllamaBot\Middlewares\{AuthUsers, FindUserOrRegister};
use Mateodioev\OllamaBot\Tavily\{Client, Response};
use Mateodioev\OllamaBot\Tavily\IncludeAnswer;
use Mateodioev\OllamaBot\Tavily\SearchDepth;
use Mateodioev\OllamaBot\Tavily\SearchSettings;
use Mateodioev\OllamaBot\Tavily\TimeRange;
use Mateodioev\TgHandler\Commands\MessageCommand;

use function Mateodioev\OllamaBot\env;

class Search extends MessageCommand
{
    protected string $name        = 'search';
    protected array  $prefix      = ['/', '!', '.'];
    protected array  $middlewares = [
        FindUserOrRegister::class,
        AuthUsers::class,
    ];

    public function execute(array $args = [])
    {
        $payload = $this->ctx()->getPayload();
        if (empty($payload)) {
            $this->onEmpty();
            return;
        }

        $tavily   = new Client(env('TAVILY_API_KEY'));
        $settings = new SearchSettings()->setDepth(SearchDepth::Advanced)
            ->setTimeRange(TimeRange::Year)
            ->setIncludeAnswer(IncludeAnswer::Advanced)
            ->setIncludeRawContent(true)
            ->setMaxResults(10);

        $response = $tavily->search($payload, $settings);

        $user = $args[FindUserOrRegister::class];
        $lang = $this->language($this->ctx()->getUser()->languageCode());

        $stream = new OllamaStreamCompletion(
            $this->api(),
            $this->ctx(),
            $user,
            $this->logger(),
        );

        $query = $this->buildQuery($response, $lang);
        $stream->run($query);
    }

    private function buildQuery(Response $response, string $language): string
    {
        $format = "Based on this information, tell me \"%s\" in %s\n\n%s";
        return sprintf(
            $format,
            $response->query,
            $language,
            (string) $response,
        );
    }

    private function language(?string $key): string
    {
        return match ($key) {
            'en'    => 'English',
            default => 'Español',
        };
    }

    private function onEmpty(): void
    {
        $this->api()->replyToMessage($this->ctx()->message, 'Por favor, escribe el término de búsqueda.');
    }
}
