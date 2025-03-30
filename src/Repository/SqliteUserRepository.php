<?php

namespace Mateodioev\OllamaBot\Repository;

use Mateodioev\OllamaBot\Db\SqliteDatabase;
use Mateodioev\OllamaBot\Models\User;

use function Mateodioev\OllamaBot\env;

class SqliteUserRepository implements UserRepository
{
    private const string FIND_SQL = "SELECT users.id, users.model, u.name as rank 
    FROM users
    INNER JOIN userRank u ON u.id = users.rank 
    WHERE users.id = ? LIMIT 1";

    private const string SAVE_SQL = "
        INSERT INTO users (id, model, rank) 
        VALUES (?, ?, ?) 
        ON CONFLICT(id) DO UPDATE SET model = excluded.model, rank = excluded.rank";

    private const string UPDATE_SQL = "UPDATE users SET model = ?, rank = ? WHERE id = ?";

    public function __construct(private SqliteDatabase $db)
    {
        $db->execute(file_get_contents(env('BASE_DIR') . 'db/main.sql'));
    }

    public function find(int $id): ?User
    {
        $results = $this->db->query(self::FIND_SQL, [$id]);
        $result  = array_shift($results) ?? [];

        if (empty($result)) {
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
            $user->id,
        ]);
    }
}
