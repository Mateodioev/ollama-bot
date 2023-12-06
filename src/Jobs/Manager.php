<?php

namespace Mateodioev\OllamaBot\Jobs;

use Revolt\EventLoop;

class Manager
{
    public function submit(Job $job)
    {
        $job->setId(
            EventLoop::repeat($job->interval(), $job->run(...))
        );
    }

    public function cancel(Job $job): void
    {
        $job->cancel();
    }
}
