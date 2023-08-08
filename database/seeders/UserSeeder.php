<?php

namespace Database\Seeders;

use App\Models\WebAdmin;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'first_name' => 'Mr.',
            'last_name' => "Admin",
            'email' => 'admin@admin.com',
            'phone' => "01792812123",
            'email_verified_at' => now(),
            'password' => '12345678', // 12345678
            'remember_token' => Str::random(10),
        ]);
        WebAdmin::create([
            'name' => 'Mr.Admin',
            'email' => 'superadmin@admin.com',
            'phone' => "01792812123",
            'email_verified_at' => now(),
            'password' => bcrypt('12345678'), // 12345678
            'remember_token' => Str::random(10),
        ]);

        User::factory(1)->create();
    }
}
