<?php

use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('provider')->insertOrIgnore([
            [
                'provider_key' => 'megacash',
                'title' => 'Megacash',
            ],[
                'provider_key' => 'supermoney',
                'title' => 'Supermoney',
            ]
        ]);
    }
}
