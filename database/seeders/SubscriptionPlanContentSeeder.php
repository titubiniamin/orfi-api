<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPlanContent;
use Illuminate\Database\Seeder;

class SubscriptionPlanContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $contents = [
            0 => 'All answers, no ads',
            1 => 'Unlimited live tutoring',
            2 => '24/7 access to tutors in math & science'
        ];
        foreach (SubscriptionPlan::all() as $subscriptionPlan) {
            foreach ($contents as $key => $content){
            SubscriptionPlanContent::create([
                'subscription_plan_id' => $subscriptionPlan->id,
                'feature_title' => $content,
                'is_active' => ($subscriptionPlan->id == 2 && $key > 0) ? 0 : 1,
                ]);
        }
        }

    }
}
