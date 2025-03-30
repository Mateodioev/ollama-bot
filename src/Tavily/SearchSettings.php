<?php

declare(strict_types=1);

namespace Mateodioev\OllamaBot\Tavily;

class SearchSettings
{
    public SearchTopic   $topic;
    public SearchDepth   $depth;
    public int           $maxResults;
    public TimeRange     $timeRange;
    public IncludeAnswer $includeAnswer;
    public bool          $includeRawContent;
    public int           $days;

    public function __construct()
    {
        $this->topic             = SearchTopic::General;
        $this->depth             = SearchDepth::Basic;
        $this->maxResults        = 5;
        $this->timeRange         = TimeRange::Month;
        $this->includeAnswer     = IncludeAnswer::Basic;
        $this->includeRawContent = false;
        $this->days              = 5;
    }

    public function build(string $query): array
    {
        $values = [
            'query'               => $query,
            'topic'               => $this->topic->topic(),
            'search_depth'        => $this->depth->depth(),
            'max_results'         => $this->maxResults,
            'time_range'          => $this->timeRange->format(),
            'include_answer'      => $this->includeAnswer->answer(),
            'include_raw_content' => $this->includeRawContent,
            'days'                => $this->days,
        ];

        return array_filter($values);
    }
}
