<?php

/** @noinspection PhpPropertyOnlyWrittenInspection */

namespace Mateodioev\OllamaBot\Db;

use PDO;
use PDOException;

class MysqlDatabase implements Database
{
    private ?PDO $pdo = null;

    public function __construct(
        private readonly string $dsn,
        private readonly string $username,
        private readonly string $password
    ) {
        try {
            $this->pdo = new PDO($dsn, $username, $password);
        } catch (PDOException $e) {
            echo 'DSN: ' . $dsn . PHP_EOL;
            echo 'username: ' . $username . PHP_EOL;
            echo 'password: ' . $password . PHP_EOL;
            throw $e;
        }
    }

    public function connect(): void
    {
    }

    public function disconnect(): void
    {
        $this->pdo = null;
    }

    public function isConnected(): bool
    {
        return $this->pdo !== null;
    }

    public function query(string $query, array $params = []): array
    {
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);

        try {
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } finally {
            $statement->closeCursor();
        }
    }

    public function execute(string $query, array $params = []): void
    {
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);
        $statement->closeCursor();
    }

    public function lastInsertId(): false|int
    {
        return $this->pdo->lastInsertId();
    }
}
