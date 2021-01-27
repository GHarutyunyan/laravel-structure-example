<?php

namespace App\Http\Controllers\Auth\Password;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Password\AuthPasswordEmailRequest;
use App\Http\Requests\Auth\Password\AuthPasswordResetRequest;
use App\Http\Requests\Auth\Password\AuthPasswordSetRequest;
use App\Http\Resources\UserResource;
use App\Services\Authentication\Contracts\AuthenticationServiceInterface as AuthenticationService;
use App\Services\Users\UserNotificationService;
use App\Services\Users\UserPasswordService;
use App\Services\Users\UserService;

class AuthPasswordController extends Controller
{
    public function set(
        AuthenticationService $authenticationService,
        UserPasswordService $userPasswordService,
        AuthPasswordSetRequest $request
    ) {
        $user = $authenticationService->user();
        $userPasswordService->updatePassword($user, $request->input('password'));
        $token = $authenticationService->generateToken($user);

        return response()->withToken($token, new UserResource($user));
    }

    public function email(
        UserService $userService,
        UserPasswordService $userPasswordService,
        UserNotificationService $userNotificationService,
        AuthPasswordEmailRequest $request
    ) {
        $user = $userService->findByEmail($request->input('email'));
        $token = $userPasswordService->generatePasswordResetToken($user);
        $userNotificationService->notifyResetPassword($user, $token);

        return response()->successMessage(trans('passwords.sent'));
    }

    /**
     * @todo Rework error handling due to upcoming guard changes.
     */
    public function reset(
        UserPasswordService $userPasswordService,
        AuthPasswordResetRequest $request
    ) {
        $userPasswordService->resetPasswordWithResetToken(
            $request->input('email'),
            $request->input('token'),
            $request->input('password')
        );

        return response()->successMessage(trans('passwords.reset'));
    }
}
