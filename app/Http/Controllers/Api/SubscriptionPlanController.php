<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionResource;
use App\Models\Payment\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPlanContent;
use App\Services\AccessTokenService;
use App\Services\SubscriptionPlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $subscriptionPlan = SubscriptionPlan::query()->active()->with('contents')->latest()->get();
        return response()->json(['subscriptionPlan' => $subscriptionPlan, 'status' => 200]);
    }

    /**
     * @return JsonResponse
     */
    public function validSubscriptionPlans(): JsonResponse
    {
        $validSubscriptionPlans = SubscriptionPlanService::getValidSubscriptionPlans();
        return response()->json(['validSubscriptionPlans' => $validSubscriptionPlans, 'status' => 200]);
    }

    /**
     * @return JsonResponse
     */
    public function subscriptionPlansHistory(): JsonResponse
    {
        $subscriptionPlansHistory = SubscriptionPlanService::getSubscriptionPlansHistory();
        return response()->json(['subscriptionPlansHistory' => $subscriptionPlansHistory, 'status' => 200]);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function updateSubscriptionPlan($id): JsonResponse
    {
        $subscriptionPlans = Subscription::query();
        $subscriptionPlans->where('user_id', auth()->id())->where('status','!=','canceled')->update(['status' => 'inactive']);
        $subscriptionPlans->whereId($id)->update(['status' => 'active']);

        $validSubscriptionPlans = SubscriptionPlanService::getValidSubscriptionPlans();
        return response()->json([
            'validSubscriptionPlans' => $validSubscriptionPlans,
            'subscription' => AccessTokenService::getActiveSubscriptionPlan(auth()->id()),
            'status' => 200]);
    }

}
