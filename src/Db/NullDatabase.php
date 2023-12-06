<?php

namespace Mateodioev\OllamaBot\Db;

final class NullDatabase implements Database
{
    public function connect(): void
    {
    }

    public function disconnect(): void
    {
    }

    public function isConnected(): bool
    {
        return false;
    }

    public function query(string $query, array $params = []): array
    {
        return [];
    }

    public function execute(string $query, array $params = []): void
    {
    }

    public function lastInsertId(): false|int
    {
        return false;
    }
}
