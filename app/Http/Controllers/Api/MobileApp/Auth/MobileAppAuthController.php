<?php

namespace App\Http\Controllers\Api\MobileApp\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Http\Requests\Auth\MobileAppResetPasswordRequest;
use App\Mail\SendOtp;
use App\Models\Auth\OTP;
use App\Models\User;
use App\Services\AccessTokenService;
use App\Services\MobileSmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


class MobileAppAuthController extends Controller
{
    /**
     * @param ForgetPasswordRequest $request
     * @return JsonResponse
     */
    public function forgotPassword(ForgetPasswordRequest $request): JsonResponse
    {
        $email = $request->email;
        $user = User::query()->where('email', $email)->first();
        if (!$user) return response()->json(["message" => 'Email does not match. Use a Valid Email', 'status' => 400]);
        $otp = rand(100000, 999999);
        OTP::updateOrCreate([
            'user_id' => $user->id,
        ], [
            'user_id' => $user->id,
            'otp' => $otp,
            'expired_at' => now()->addMinute(5)
        ]);
        if ($user->phone) {
            $message = "Your OTP is " . $otp . " for reset password on ORFI App. This OTP will valid for 5 minutes.";
            MobileSmsService::singleSendSms($user->phone, $message);
        }
        Mail::to($email)->queue(new SendOtp($otp));
        return response()->json(["email" => $email, "message" => 'We have already send a OTP on your mobile & email.', 'status' => 200]);
    }

    /**
     * @param MobileAppResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(MobileAppResetPasswordRequest $request): JsonResponse
    {
        $otp = $request->otp;
        $user = User::where('email', $request->email);

        $otpValidation = $user->whereHas('otp', function ($query) use ($otp) {
            $query->where('otp', $otp);
        })->first();
        if (!$otpValidation) return response()->json(["message" => 'OTP does not match. Resend OTP.', 'status' => 400]);

        $otpTimeValidation = $user->whereHas('otp', function ($query) use ($otp) {
            $query->where('expired_at', '>=', now());
        })->first();
        if (!$otpTimeValidation) return response()->json(["message" => 'OTP time expired. Resend OTP.', 'status' => 400]);

        $user->update(['password' => Hash::make($request->password, ['rounds' => 12])]);
        $user = $user->first();
        OTP::query()->where('user_id', $user->id)->delete();
        return AccessTokenService::getAccessToken($user);
    }
}
