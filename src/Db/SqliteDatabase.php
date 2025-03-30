<?php

namespace Mateodioev\OllamaBot\Db;

use RuntimeException;
use SQLite3;

class SqliteDatabase implements Database
{
    private ?SQLite3 $connection   = null;
    private string   $databaseFile;

    public function __construct(string $databaseFile = ':memory:')
    {
        $this->databaseFile = $databaseFile;
    }

    public function connect(): void
    {
        if ($this->connection === null) {
            $this->connection = new SQLite3($this->databaseFile);
        }
    }

    public function disconnect(): void
    {
        if ($this->connection) {
            $this->connection->close();
            $this->connection = null;
        }
    }

    public function isConnected(): bool
    {
        return $this->connection !== null;
    }

    /**
     * @inheritDoc
     */
    public function query(string $query, array $params = []): array
    {
        $this->connect();
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new RuntimeException('Failed to prepare statement: ' . $this->connection->lastErrorMsg());
        }
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value, $this->getType($value));
        }
        $result = $stmt->execute();
        $rows   = [];
        while ($row = $result->fetchArray(\SQLITE3_ASSOC)) {
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * @inheritDoc
     */
    public function execute(string $query, array $params = []): void
    {
        $this->connect();
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new RuntimeException('Failed to prepare statement: ' . $this->connection->lastErrorMsg());
        }
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value, $this->getType($value));
        }
        $stmt->execute();
    }

    public function lastInsertId(): false|int
    {
        $this->connect();
        return $this->connection->lastInsertRowID();
    }

    private function getType($value): int
    {
        if (is_int($value)) {
            return SQLITE3_INTEGER;
        } elseif (is_float($value)) {
            return SQLITE3_FLOAT;
        } elseif ($value === null) {
            return SQLITE3_NULL;
        }
        return SQLITE3_TEXT;
    }
}
