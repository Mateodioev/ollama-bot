<?php

namespace Mateodioev\OllamaBot\Jobs;

use Revolt\EventLoop;

abstract class abstractJob implements Job
{
    private string $id;

    public function id(): string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function cancel(): void
    {
        EventLoop::cancel($this->id());
    }
}
