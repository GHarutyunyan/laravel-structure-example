<?php

namespace App\Repositories;

use App\Repositories\AbstractRepository as Repository;
use Illuminate\Database\Eloquent\Collection;

class PasswordResetRepository extends Repository
{
    public function getExpiredPasswordResets(int $tokensExpireInMinutes, int $take): Collection
    {
        return $this
            ->take($take)
            ->where('updated_at', '<', now()->subMinutes($tokensExpireInMinutes))
            ->get()
            ->getHandle();
    }
}
