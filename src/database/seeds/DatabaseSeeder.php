<?php

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
        if (config('app.env') !== 'production') {
            $this->call(ProviderSeeder::class);
            $this->call(ErrorCodeSeeder::class);
        }
    }
}
