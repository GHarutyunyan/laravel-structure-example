<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository extends AbstractRepository implements UserRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function userNicknameExists(string $userName): bool
    {
        return $this->whereNickname($userName)->exists();
    }

    public function findByEmail(string $email)
    {
        return $this->where(['email' => $email]);
    }

    public function findBySocialToken(string $token)
    {
        return $this->whereHas('socialAccounts', function ($query) use ($token) {
            $query->where(['token' => $token]);
        });
    }

    public function findBySocialTypeSlug(string $socialTypeSlug)
    {
        return $this->whereHas('socialAccounts.socialType', function ($query) use ($socialTypeSlug) {
            $query->where(['slug' => $socialTypeSlug]);
        });
    }

    /**
     * Find user by a valid account token.
     *
     * @param string $accountToken
     *
     * @return \App\Models\User|void
     */
    public function findByAccountToken(string $accountToken)
    {
        $user = $this
            ->where('account_token', $accountToken)
            ->where('account_token_expires', '>', now())
            ->first();

        return $user ? $user->getHandle() : null;
    }

    /**
     * Find user by socal type and token.
     *
     * @param string $socialType
     * @param string $socialId
     *
     * @return \App\Models\User|null
     */
    public function findBySocialTypeAndSocialToken(string $socialType, string $socialToken)
    {
        $user = $this
            ->findBySocialTypeSlug($socialType)
            ->findBySocialToken($socialToken)
            ->first();

        return $user ? $user->getHandle() : null;
    }
}
