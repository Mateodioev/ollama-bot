<?php

namespace Mateodioev\OllamaBot\Repository;

use Mateodioev\OllamaBot\Db\Database;
use Mateodioev\OllamaBot\Models\User;

class MysqlUserRepository implements UserRepository
{
    private const FIND_SQL   = "SELECT users.id, users.model, u.name as rank 
    FROM users
    INNER JOIN userRank u ON u.id = users.rank 
    WHERE users.id = ? LIMIT 1";
    private const SAVE_SQL   = "INSERT INTO users (id, model, rank) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE model = ?, rank = ?";
    private const UPDATE_SQL = "UPDATE users SET model = ?, rank = ? WHERE id = ?";

    public function __construct(
        private Database $db
    ) {
    }

    public function find(int $id): ?User
    {
        $result = $this->db->query(self::FIND_SQL, [$id]);
        $result = $result[0] ?? [];

        if (empty(($result))) {
            return null;
        }

        return new User(
            $result['id'],
            $result['model'],
            $result['rank']
        );
    }

    public function save(User $user): void
    {
        $this->db->execute(self::SAVE_SQL, [
            $user->id,
            $user->model,
            $user->rank->value,
            $user->model,
            $user->rank->value,
        ]);
    }

    public function update(User $user): void
    {
        $this->db->execute(self::UPDATE_SQL, [
            $user->model,
            $user->rank->value,
            $user->id
        ]);
    }
}
