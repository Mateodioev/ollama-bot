<?php

namespace Test;

use Mateodioev\OllamaBot\Models\User;
use Mateodioev\OllamaBot\Repository\UserRepository;

class MemoryUserRepository implements UserRepository
{
    private array $users = [];

    public function find(int $id): ?User
    {
        return $this->users[$id] ?? null;
    }

    public function save(User $user): void
    {
        $this->users[$user->id] = $user;
    }

    public function update(User $user): void
    {
        $this->users[$user->id] = $user;
    }
}
