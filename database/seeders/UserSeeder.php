<?php

namespace Database\Seeders;

use App\Models\User;
use Bouncer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Silber\Bouncer\Database\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        if (! User::where('email', 'admin@admin.com')->exists()) {
            User::factory()->create(['email' => 'admin@admin.com', 'first_name' => 'amir', 'last_name' => 'alizadeh', 'password' => bcrypt('123456')]);
            Artisan::call('sanjab:make:admin', ['--user' => 'admin@admin.com']);
        }

        User::factory(100)->create();
    }
}
