<?php

declare(strict_types=1);

namespace Mateodioev\OllamaBot\Tavily;

enum IncludeAnswer: string
{
    case None     = 'none';
    case Basic    = 'basic';
    case Advanced = 'advanced';

    public function answer(): string
    {
        if ($this === self::None) {
            return '';
        }
        return $this->value;
    }
}
