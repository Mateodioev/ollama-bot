<?php

namespace Mateodioev\OllamaBot\Db;

interface Database
{
    public function connect(): void;

    public function disconnect(): void;

    public function isConnected(): bool;

    /**
     * Executes a query and returns the result as an array of associative arrays.
     */
    public function query(string $query, array $params = []): array;

    /**
     * Executes a query
     */
    public function execute(string $query, array $params = []): void;

    public function lastInsertId(): false|int;
}
