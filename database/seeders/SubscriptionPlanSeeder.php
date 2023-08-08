<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subscriptionPlans = [
            [
                'title' => 'Premium',
                'description' => "Get 25% Discount on every Courses under this plan.",
                'type' => 'premium',
                'amount' => 500,
                'color' => "#f58220",
                'active_color' => "#f8f0ea",
                'duration' => 1,
                'duration_type' => "month",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Basic',
                'description' => "Get 10% Discount on every Courses under this plan.",
                'type' => 'basic',
                'amount' => 250,
                'color' => "#0095d0",
                'active_color' => "#f7f8f6",
                'duration' => 1,
                'duration_type' => "month",
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        SubscriptionPlan::insert($subscriptionPlans);
    }
}
