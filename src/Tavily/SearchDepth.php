<?php

declare(strict_types=1);

namespace Mateodioev\OllamaBot\Tavily;

/**
 * The depth of the search.
 * Advance search is tailored to retrieve the most relevant sources and content snippets for you query
 * While basic search provides generic content snippets from each source
 */
enum SearchDepth: string
{
    case Basic    = 'basic';
    case Advanced = 'advanced';

    public function depth(): string
    {
        if ($this === self::Basic) {
            return '';
        }
        return $this->value;
    }
}
