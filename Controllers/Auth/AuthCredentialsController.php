<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Credentials\AuthCredentialsSignInRequest;
use App\Http\Requests\Auth\Credentials\AuthCredentialsSignUpRequest;
use App\Http\Requests\Auth\Credentials\AuthCredentialsVerifyAccountRequest;
use App\Http\Resources\UserResource;
use App\Services\Authentication\Contracts\AuthenticationServiceInterface as AuthenticationService;
use App\Services\Users\UserNotificationService;
use App\Services\Users\UserProfileService;
use App\Services\Users\UserRegistrationService;
use App\Services\Users\UserService;
use App\Services\Users\UserTooltipService;

class AuthCredentialsController extends Controller
{
    /**
     * Signup with credentials, set initial tooltip, send verification notification and generate access token.
     *
     * @param \App\Services\Authentication\Contracts\AuthenticationServiceInterface $authenticationService
     * @param \App\Services\Users\UserRegistrationService $userRegistrationService
     * @param \App\Services\Users\UserTooltipService $userTooltipService
     * @param \App\Http\Requests\Auth\Credentials\AuthCredentialsSignUpRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function signUp(
        AuthenticationService $authenticationService,
        UserRegistrationService $userRegistrationService,
        UserNotificationService $userNotificationService,
        UserTooltipService $userTooltipService,
        AuthCredentialsSignUpRequest $request
    ) {
        $user = $userRegistrationService->registerWithCredentials(
            $request->input('email'),
            $request->input('name'),
            $request->input('password')
        );

        $userNotificationService->notifyVerification($user);
        $userTooltipService->setInitialUserTooltip($user);
        $token = $authenticationService->generateToken($user);

        return response()->withToken($token, new UserResource($user));
    }

    /**
     * Sign in with credentials, generate access token and load profile relations.
     *
     * @param \App\Services\Authentication\Contracts\AuthenticationServiceInterface $authenticationService
     * @param \App\Services\Users\UserService $userService
     * @param \App\Services\Users\UserProfileService $userProfileService
     * @param \App\Http\Requests\Auth\Credentials\AuthCredentialsSignInRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function signIn(
        AuthenticationService $authenticationService,
        UserService $userService,
        UserProfileService $userProfileService,
        AuthCredentialsSignInRequest $request
    ) {
        $user = $userService->findByCredentials($request->input('email'), $request->input('password'));

        if (!$user) {
            return response()->unauthenticated(trans('auth.failed'));
        }

        $token = $authenticationService->generateToken($user);
        $userProfileService->loadProfileRelations($user);

        return response()->withToken($token, new UserResource($user));
    }

    /**
     * Verify user with account token and generate access token.
     *
     * @param \App\Services\Authentication\Contracts\AuthenticationServiceInterface $authenticationService
     * @param \App\Services\Users\UserRegistrationService $userRegistrationService
     * @param \App\Http\Requests\Auth\Credentials\AuthCredentialsVerifyAccountRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyAccount(
        AuthenticationService $authenticationService,
        UserRegistrationService $userRegistrationService,
        AuthCredentialsVerifyAccountRequest $request
    ) {
        $user = $userRegistrationService->verifyAccount($request->input('token'));
        $token = $authenticationService->generateToken($user);

        return response()->withToken($token, new UserResource($user));
    }
}
