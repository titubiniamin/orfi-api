<?php

namespace App\Services;

use App\Http\Resources\SubscriptionResource;
use App\Http\Resources\UserResource;
use App\Models\BlockUser;
use App\Models\Payment\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Jenssegers\Agent\Agent;
use Stevebauman\Location\Facades\Location;

class AccessTokenService
{
    /**
     * @param $userInfo
     * @return JsonResponse
     */
    public static function getAccessToken($userInfo): JsonResponse
    {
        if (BlockUser::where('user_id', $userInfo->id)->exists()) {
            return response()->json(['message' => 'Your currently baned from this application.Please contact our customer support.', 'status' => 403]);
        }
        $agent = new Agent();
        $tokenResult = $userInfo->createToken("{$agent->deviceType()}");
        $token = $tokenResult->token;
        $token->expires_at = request()->filled('remember_me') ? $token->expires_at = now()->addMonth(1) : now()->addWeeks(1);

        $token->save();

        return self::getUserInfo($userInfo, $tokenResult->accessToken, $tokenResult->token->id);
    }

    /**
     * @param $user
     * @param $accessToken
     * @param $tokenId
     * @return JsonResponse
     */
    public static function getUserInfo($user, $accessToken, $tokenId): JsonResponse
    {
        if (BlockUser::where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Your currently baned from this application.Please contact our customer support.', 'status' => 403]);
        }
        return response()->json([
            'user' => UserResource::make($user),
            'subscription' => self::getActiveSubscriptionPlan($user->id),
            'access_token' => $accessToken,
            'tokenId' => $tokenId,
            'status' => 200,
            'token_type' => 'Bearer',
        ]);
    }

    public static function getActiveSubscriptionPlan($user_id)
    {
        $subscription = Subscription::query()
            ->where(['user_id' => $user_id, 'status' => 'active'])
            ->whereDate('expired_at', '>=', now())
            ->with('subscriptionPlan')
            ->orderByDesc('id')
            ->first();

        return $subscription ? SubscriptionResource::make($subscription) : null;
    }

    public static function checkingTooMuchAttempts($request)
    {
        if (Cache::get('unauthorized' . $request->ip()) > 6) {
            $user = User::query()->where('email', $request->email_or_phone)
                ->orWhere('phone', $request->email_or_phone)
                ->first();
            if ($user) {
                $agent = new Agent();
                BlockUser::updateOrCreate(['user_id' => $user->id], [
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'platform' => $agent->device().', '. $agent->deviceType().', ' .$agent->platform(),
                    'location' => Location::get($request->ip()),
                    'reason' => 'Too many login attempts'
                ]);
            }
            return response()->json(['message' => 'Too many attempts.', 'details' => 'Too many attempts. Please try again later.', 'status' => 401]);
        }
        return false;
    }
}
