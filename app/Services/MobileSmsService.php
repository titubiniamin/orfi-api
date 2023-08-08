<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MobileSmsService
{
    public static function singleSendSms($mobileNumber, $message)
    {
        $params = [
            "api_key" => env('SMS_GATEWAY_API_KEY'),
            "type" => "text/unicode",
            "contacts" => strlen($mobileNumber) <= 11 ? '88' . $mobileNumber : $mobileNumber,
            "senderid" => env('SMS_GATEWAY_SENDER_ID'),
            "msg" => $message,
            "label" => "ORFI App"
        ];

        Http::post(env('SMS_GATEWAY_URL'), $params);
    }
}
