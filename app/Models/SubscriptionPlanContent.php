<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPlanContent extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = ['subscription_plan_id','feature_title','is_active'];

    /**
     * @return BelongsTo
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class,'subscription_plan_id','id');
    }
}
