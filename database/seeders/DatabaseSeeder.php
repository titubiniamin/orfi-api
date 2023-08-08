<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use App\Models\User;
use Database\Factories\TestimonialFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(SubscriptionPlanSeeder::class);
        $this->call(SubscriptionPlanContentSeeder::class);
        $this->call(SettingSeeder::class);
        Testimonial::factory(10)->create();


        //Run passport install command.
        Artisan::call('passport:install');
    }
}
