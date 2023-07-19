<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        \App\Models\Admin::factory()->create([
            'name'      => 'Admin',
            'email'     => 'admin@barternp.test',
            'password'  => bcrypt(12345678),
        ]);

        \App\Models\User::factory(20)->create();

        \App\Models\User::factory()->create([
            'name' => 'User',
            'email' => 'user@barternp.test',
            'phone'  => '9876543210',
            'status' => 'active',
            'otp'  => '12345',
            'password' => bcrypt(12345678),
            'gender'  => 'Female',
        ]);

        // \App\Models\Category::factory(2)->create();

        // $category = Arr::random(\App\Models\Category::inRandomOrder()->pluck('id')->toArray());

    //     error_log($category);
        // \App\Models\SubCategory::factory(3)->create();

    //     $subcategory = Arr::random(\App\Models\SubCategory::inRandomOrder()->pluck('id')->toArray());

        // \App\Models\Field::factory(5)->create();
    
        // \App\Models\Ad::factory(4)->create();

        \App\Models\Banner::factory(2)->create();
        \App\Models\InAppAdd::factory(4)->create();

    }
}
