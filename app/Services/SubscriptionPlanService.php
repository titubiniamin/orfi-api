<?php

namespace App\Services;

use App\Models\Payment\Subscription;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class SubscriptionPlanService
{
    /**
     * @return Collection
     */
    public static function getValidSubscriptionPlans(): Collection
    {
        return Subscription::query()
            ->where('user_id', auth()->id())
            ->whereDate('expired_at', '>=', now())
            ->with('subscriptionPlan.contents')
            ->latest()
            ->get(['id', 'subscription_plan_id', 'expired_at', 'status']);
    }

    /**
     * @return Collection
     */
    public static function getSubscriptionPlansHistory(): Collection
    {
        return Subscription::query()
            ->where('user_id', auth()->id())
            ->with('subscriptionPlan:id,title,amount,duration_type')
            ->latest()
            ->get()
            ->take(20);
    }

}
