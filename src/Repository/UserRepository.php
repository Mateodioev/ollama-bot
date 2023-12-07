<?php

namespace Mateodioev\OllamaBot\Repository;

use Mateodioev\OllamaBot\Models\User;

interface UserRepository
{
    public function find(int $id): ?User;

    public function save(User $user): void;

    public function update(User $user): void;
}
