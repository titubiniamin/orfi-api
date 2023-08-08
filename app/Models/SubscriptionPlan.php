<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class SubscriptionPlan extends Model
{
    use HasFactory;
    use CrudTrait;

    protected $fillable = ['title', 'description', 'type', 'amount', 'discount_amount', 'color','active_color', 'duration','duration_type','is_active'];

    /**
     * @return HasMany
     */
    public function contents(): HasMany
    {
        return $this->hasMany(SubscriptionPlanContent::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
