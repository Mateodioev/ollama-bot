<?php

namespace Mateodioev\OllamaBot\Ollama;

use function array_map;
use function json_encode;

class Chat
{
    private array $messages = [];

    public static function new(): static
    {
        return new static();
    }

    public function addMessage(Message $message): static
    {
        $this->messages[] = $message;
        return $this;
    }

    public function toJson(): string
    {
        $messages = array_map(
            fn (Message $message) => ['role' => $message->role, 'content' => $message->content],
            $this->messages
        );

        return json_encode(['messages' => $messages]);
    }
}
