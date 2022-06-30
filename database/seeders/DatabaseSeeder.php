<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Post::factory(100)->create();
        Artisan::call('laravel-elasticsearch:utils:index-delete posts_index');
    }
}
