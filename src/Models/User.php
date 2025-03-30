<?php

namespace Mateodioev\OllamaBot\Models;

use JsonSerializable;
use Stringable;

class User implements JsonSerializable, Stringable
{
    public UserRank $rank;

    public array $lastContext = [];

    public function __construct(
        public int $id,
        public string $model,
        int $rank
    ) {
        $this->rank = UserRank::try($rank);
    }

    public function canAccess(UserRank $permission): bool
    {
        return UserRank::hasPermission($this, $permission);
    }

    public function jsonSerialize()
    {
        return [
            'id'    => $this->id,
            'model' => $this->model,
            'rank'  => $this->rank->value,
        ];
    }

    public function __tostring(): string
    {
        return json_encode($this->jsonSerialize(), JSON_PRETTY_PRINT);
    }
}
