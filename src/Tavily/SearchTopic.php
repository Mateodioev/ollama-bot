<?php

declare(strict_types=1);

namespace Mateodioev\OllamaBot\Tavily;

enum SearchTopic: string
{
    case News    = 'news';
    case General = 'general';
    case Finance = 'finance';

    /**
     * Return the topic name for the enum value. If the topic is general, an empty string is returned.
     */
    public function topic(): string
    {
        if ($this === self::General) {
            return '';
        }

        return $this->value;
    }
}
