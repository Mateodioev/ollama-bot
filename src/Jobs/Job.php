<?php

namespace Mateodioev\OllamaBot\Jobs;

interface Job
{
    public function interval(): float;

    /**
     * Run the job
     */
    public function run(): void;

    /**
     * Get job ID to cancel
     */
    public function id(): string;

    public function setId(string $id): static;

    public function cancel(): void;
}
