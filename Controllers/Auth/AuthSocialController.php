<?php

namespace App\Http\Controllers\Auth;

use App\Factories\SocialProviderFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Social\AuthSocialSingInRequest;
use App\Http\Resources\UserResource;
use App\Services\Authentication\Contracts\AuthenticationServiceInterface as AuthenticationService;
use App\Services\Users\UserRegistrationService;
use App\Services\Users\UserTooltipService;

class AuthSocialController extends Controller
{
    /**
     * Sign in with social.
     *
     * @param AuthSocialSingInRequest $request
     * @param AuthenticationService $authenticationService
     * @param SocialProviderFactory $socialProviderFactory
     * @param UserRegistrationService $userRegistrationService
     * @param UserTooltipService $userTooltipService
     * @return mixed
     * @throws \App\Factories\Exceptions\UndefinedSocialProviderException
     * @throws \ErrorException
     */
    public function signIn(
        AuthSocialSingInRequest $request,
        AuthenticationService $authenticationService,
        SocialProviderFactory $socialProviderFactory,
        UserRegistrationService $userRegistrationService,
        UserTooltipService $userTooltipService
    ) {

        $user = $userRegistrationService->signInWithSocial(
            $socialProviderFactory->make($request->input('social_type')),
            $request->input('social_id'),
            $request->input('token')
        );

        if ($user->wasRecentlyCreated) {
            $userTooltipService->setInitialUserTooltip($user);
        }

        $token = $authenticationService->generateToken($user);

        return response()->withToken($token, new UserResource($user));
    }
}
