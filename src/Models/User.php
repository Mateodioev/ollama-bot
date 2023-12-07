<?php

namespace Mateodioev\OllamaBot\Models;

class User
{
    public UserRank $rank;
    public OllamaModels $model;

    public array $lastContext = [];

    public function __construct(
        public int $id,
        int $model,
        int $rank
    ) {
        $this->model = OllamaModels::try($model);
        $this->rank  = UserRank::try($rank);
    }

    public function canAccess(UserRank $permission): bool
    {
        return UserRank::hasPermission($this, $permission);
    }
}
