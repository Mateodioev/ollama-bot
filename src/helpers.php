<?php

namespace Mateodioev\OllamaBot;

/**
 * Get the value of an environment variable or return a default value.
 */
function env(string $key, $default = null): mixed
{
    return $_ENV[$key] ?? $default;
}

/**
 * Get all the environment variables for the application.
 */
function allEvn(): array
{
    return [...$_ENV, ...getenv()];
}
