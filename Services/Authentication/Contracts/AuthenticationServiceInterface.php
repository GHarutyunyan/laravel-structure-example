<?php

namespace App\Services\Authentication\Contracts;

use App\Models\User;
use Laravel\Passport\PersonalAccessTokenResult;

interface AuthenticationServiceInterface
{
    /**
     * Get currently authenticated user.
     *
     * @return \App\Models\User|null
     */
    public function user();

    /**
     * Generate authentication bearer token
     *
     * @param \App\Models\User $user
     *
     * @return \Laravel\Passport\PersonalAccessTokenResult
     */
    public function generateToken(User $user): PersonalAccessTokenResult;

    /**
     * Revoke actual user token.
     *
     * @param \App\Models\User $user
     *
     * @return void
     */
    public function revokeToken(User $user): void;
}
