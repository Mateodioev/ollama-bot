<?php

/** @noinspection PhpPropertyOnlyWrittenInspection */

namespace Mateodioev\OllamaBot\Db;

use PDO;
use PDOException;
use RuntimeException;
use function Mateodioev\OllamaBot\env;

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
            $this->query(file_get_contents(env('BASE_DIR') . "db/main.sql"));
        } catch (PDOException $e) {
            echo 'DSN: ' . $dsn . PHP_EOL;
            echo 'username: ' . $username . PHP_EOL;
            echo 'password: ' . $password . PHP_EOL;
            throw $e;
        }
    }

    /**
     * @throws RuntimeException
     */
    public function connect(): void
    {
        if ($this->pdo === null) {
            try {
                $this->pdo = new PDO($this->dsn, $this->username, $this->password);
            } catch (PDOException $e) {
                throw new RuntimeException('Connection failed: ' . $e->getMessage(), previous: $e);
            }
        }
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
