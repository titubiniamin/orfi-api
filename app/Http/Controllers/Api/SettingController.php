<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\SubscriptionPlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function homePageSetting(): JsonResponse
    {
        $settings = Setting::first();
        return response()->json(['setting' => $settings, 'status' => 200]);
    }
}
