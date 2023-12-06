<?php

namespace Mateodioev\OllamaBot\Ollama;

class Message
{
    public function __construct(
        public string $role,
        public string $content
    ) {
    }

    public static function fromArray(array $parameters): self
    {
        return new self(
            $parameters['role'],
            $parameters['content']
        );
    }
}
