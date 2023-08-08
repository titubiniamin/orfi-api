<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\EmailVerificationNotification;
use App\Mail\SendOtp;
use App\Models\Auth\OTP;
use App\Models\User;
use App\Services\AccessTokenService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EmailVerificationController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendVerificationEmail(Request $request) : JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already Verified'], 200);
        }

        EmailVerificationNotification::dispatch($request->user());
        return response()->json([
            'message' => 'Verification link send on your email address.Please check your email.',
            'status' => 200
        ]);
    }

    /**
     * @param EmailVerificationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailVerify(EmailVerificationRequest $request) : JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified.'
            ]);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return response()->json([
            'message' => 'Email has been verified.'
        ]);
    }

    public function appSendVerificationEmail(Request $request)
    {
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already Verified'], 200);
        }
        $otp = rand(100000, 999999);
        OTP::updateOrCreate([
            'user_id' => $user->id,
        ], [
            'user_id' => $user->id,
            'otp' => $otp,
            'expired_at' => now()->addMinute(5)
        ]);

        Mail::to($user->email)->queue(new SendOtp($otp));

        return response()->json([
            'message' => 'Verification OTP send on your email address.Please check your email.',
            'status' => 200
        ]);

    }

    public function appEmailVerify(Request $request)
    {
        $otp = $request->otp;
        $requestUser = $request->user();
        $user = User::where('email', $requestUser->email);

        $otpValidation = $user->whereHas('otp', function ($query) use ($otp) {
            $query->where('otp', $otp);
        })->first();

        if (!$otpValidation) return response()->json(["message" => 'OTP does not match. Resend OTP.', 'status' => 400]);

        $otpTimeValidation = $user->whereHas('otp', function ($query) use ($otp) {
            $query->where('expired_at', '>=', now());
        })->first();

        if (!$otpTimeValidation) return response()->json(["message" => 'OTP time expired. Resend OTP.', 'status' => 400]);

        $user->update(['email_verified_at' => now()]);
        $user = $user->first();
        OTP::query()->where('user_id', $user->id)->delete();
        return AccessTokenService::getAccessToken($user);
    }
}
