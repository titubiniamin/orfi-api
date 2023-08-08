<?php

namespace App\Http\Resources;


use Decimal\Decimal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Ramsey\Uuid\Type\Integer;

class SubscriptionResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'is_active' => $this->is_active,
            'expired_at' => $this->expired_at,
            'token' => $this->token,
            'subscriptionPlanId' => $this->subscriptionPlan->id,
            'subscriptionPlanAmount' => (Float) $this->subscriptionPlan->amount,
            'subscriptionPlanTitle' => $this->subscriptionPlan->title,
            'subscriptionPlanType' => $this->subscriptionPlan->type,
            'subscriptionPlanDiscountAmount' => (Float) $this->subscriptionPlan->discount_amount,
            'subscriptionPlanDuration' => $this->subscriptionPlan->duration,
            'subscriptionPlanDurationType' => $this->subscriptionPlan->duration_type,
            'subscriptionPlanColor' => $this->subscriptionPlan->color,
        ];
    }
}
