<?php

declare(strict_types=1);

namespace Mateodioev\OllamaBot\Tavily;

enum TimeRange: string
{
    case None  = 'none';
    case Day   = 'day';
    case Week  = 'week';
    case Month = 'month';
    case Year  = 'year';

    public function format(): string
    {
        if ($this === self::None) {
            return '';
        }

        return $this->value;
    }
}
