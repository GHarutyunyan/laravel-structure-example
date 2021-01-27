<?php

namespace App\Services\Authentication;

use App\Models\User;
use App\Services\Authentication\Contracts\AuthenticationServiceInterface;
use Illuminate\Contracts\Auth\Guard as AuthGuard;
use Laravel\Passport\PersonalAccessTokenResult;

class AuthenticationService implements AuthenticationServiceInterface
{
    /**
     * @var \Illuminate\Contracts\Auth\Guard
     */
    private $auth;

    /**
     * @param \Illuminate\Contracts\Auth\Guard $auth
     */
    public function __construct(AuthGuard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @inheritDoc
     */
    public function user()
    {
        return $this->auth->check() ? $this->auth->user() : null;
    }

    /**
     * @inheritDoc
     */
    public function generateToken($user): PersonalAccessTokenResult
    {
        return $user->createToken('auth');
    }

    /**
     * @inheritDoc
     */
    public function revokeToken(User $user): void
    {
        if ($token = $user->token()) {
            $token->revoke();
        }
    }
}
