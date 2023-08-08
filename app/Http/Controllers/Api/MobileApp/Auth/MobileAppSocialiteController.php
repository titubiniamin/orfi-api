<?php

namespace App\Http\Controllers\Api\MobileApp\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SocialMobileAppRequest;
use App\Models\User;
use App\Services\AccessTokenService;

class MobileAppSocialiteController extends Controller
{
    /**
     * @param SocialMobileAppRequest $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function socialLoginRegistration(SocialMobileAppRequest $request)
    {
        if (count($request->all())) {
            //Checking if the user is new
            $user = User::where('email', $request->email)->first();
            //If user is new then register
            if (empty($user)) {
                $user = User::create($request->except('password'));
            }
            // return Access Token
            return AccessTokenService::getAccessToken($user);
        }
    }
}
