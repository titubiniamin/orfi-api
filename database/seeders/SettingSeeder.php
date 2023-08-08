<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            [
                'title' => 'Ask to ORFI & Get Answer',
                'short_description' => "The platform is used by learners who are looking to learn or prepare for a competitive exam. The content on the platform is prepared by star educators who are passionate about teaching",
                'header_color' => '#221f1c',
                'background_image' => 'images/settings/default-background.jpg',
                'logo' => 'images/settings/default-logo.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        Setting::insert($settings);
    }
}
