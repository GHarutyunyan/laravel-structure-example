<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\Authentication\Contracts\AuthenticationServiceInterface as AuthenticationService;

class AuthAccessTokenController extends Controller
{
    /**
     * Refresh user's current access token.
     *
     * @param \App\Services\Authentication\Contracts\AuthenticationServiceInterface $authService
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(AuthenticationService $authService)
    {
        $user = $authService->user();

        $authService->revokeToken($user);
        $token = $authService->generateToken($user);

        return response()->withToken($token, new UserResource($user));
    }

    /**
     * Revoke user's current access token.
     *
     * @param \App\Services\Authentication\Contracts\AuthenticationServiceInterface $authService
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function revoke(AuthenticationService $authService)
    {
        $user = $authService->user();
        $authService->revokeToken($user);

        return response()->successMessage(trans('auth.logged_out'));
    }
}
