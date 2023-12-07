<?php

namespace Mateodioev\OllamaBot\Models;

class User
{
    public UserRank $rank;

    public array $lastContext = [];

    public function __construct(
        public int $id,
        public string $model,
        int $rank
    ) {
        $this->rank  = UserRank::try($rank);
    }

    public function canAccess(UserRank $permission): bool
    {
        return UserRank::hasPermission($this, $permission);
    }
}
