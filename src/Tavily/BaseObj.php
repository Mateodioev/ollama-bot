<?php

declare(strict_types=1);

namespace Mateodioev\OllamaBot\Tavily;

use JsonSerializable;
use Stringable;

class BaseObj implements Stringable, JsonSerializable
{
    protected array $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function __debugInfo(): array
    {
        return $this->data;
    }

    public function __get(string $name): mixed
    {
        return $this->data[$name] ?? null;
    }

    public function __tostring()
    {
        return json_encode($this->data, flags: JSON_PRETTY_PRINT);
    }

    public static function map(array $data): static
    {
        return new static($data);
    }

    /**
     * @return static[]
     */
    public static function mapArray(array $data): array
    {
        return array_map([static::class, 'map'], $data);
    }
    /**
     * @inheritDoc
     */
    public function jsonSerialize(): mixed
    {
        return $this->data;
    }
}
