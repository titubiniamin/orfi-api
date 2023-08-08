<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\UserLoginRequest;
use App\Http\Requests\Auth\UserRegistrationRequest;
use App\Models\Auth\OauthAccessToken;
use App\Models\BlockUser;
use App\Models\User;
use App\Notifications\WelcomeEmailNotification;
use App\Services\AccessTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Password;
use Lcobucci\JWT\Configuration;


class AuthController extends Controller
{

    /**
     * @param UserLoginRequest $request
     * @return JsonResponse
     */
    public function login(UserLoginRequest $request): JsonResponse
    {
        // Checking this email_or_phone is Email or Phone Number.
        if (filter_var($request->email_or_phone, FILTER_VALIDATE_EMAIL)) $credentials['email'] = $request->email_or_phone;
        else $credentials['phone'] = $request->email_or_phone;

        $credentials['password'] = $request->password;

        if (!Auth::attempt($credentials)) {
            Cache::remember('unauthorized' . $request->ip(), now()->addMinute(30), fn() => 0);
            $tern = Cache::increment('unauthorized' . $request->ip());   //incrementing the value of the key
            $tern = 6 - $tern;
            $checkingTooMuchAttempts = AccessTokenService::checkingTooMuchAttempts($request);
            if ($checkingTooMuchAttempts) return $checkingTooMuchAttempts;
            return response()->json([
                'message' => 'Unauthorized',
                'details' => "Email/Phone number and Password not match.You have - {$tern} change left. After that, your account will be Temporarily Locked.",
                'status' => 401
            ]);
        }
        Cache::forget('unauthorized' . $request->ip());
        $user = $request->user();
        return AccessTokenService::getAccessToken($user);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
//        OauthAccessToken::where('user_id', Auth::id())->delete();
        $request->user()->token()->delete();
        return response()->json(['message' => 'Successfully logged out', 'status' => 200]);
    }


    /**
     * @param UserRegistrationRequest $request
     * @return JsonResponse
     */
    public function registration(UserRegistrationRequest $request): JsonResponse
    {
        $user = User::create($request->all());
        return AccessTokenService::getAccessToken($user);
    }

    /**
     * @param ForgetPasswordRequest $request
     * @return JsonResponse
     */
    public function forgetPassword(ForgetPasswordRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->count();

        if (!$user) return response()->json(["message" => 'Email does not match. Use a valid email address.', 'status' => 400]);

        Password::sendResetLink($request->all());

        return response()->json(["message" => 'Reset password link sent on your email address.', 'status' => 200]);
    }

    /**
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $reset_password_status = Password::reset($request->all(), function ($user, $password) {
            $user->password = $password;
            $user->save();
        });

        if ($reset_password_status == Password::INVALID_TOKEN) {
            return response()->json(["message" => "Invalid token provided", 'status' => 401]);
        }

        return response()->json(["message" => "Password has been successfully changed.", 'status' => 200]);
    }

    /**
     * @return JsonResponse|void
     */
    public function checkToken(Request $request)
    {
        if (!Auth::guard('api')->check())
            return response()->json(["message" => "Token was not validate", "status" => 401]);
        else {
            $user = auth()->guard('api')->user();
            $bearerToken = $request->bearerToken();
            $tokenId = Configuration::forUnsecuredSigner()->parser()->parse($bearerToken)->claims()->get('jti');
            return AccessTokenService::getUserInfo($user, $bearerToken, $tokenId);
        }
    }

}
