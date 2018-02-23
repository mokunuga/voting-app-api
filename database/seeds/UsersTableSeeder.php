<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $faker = \Faker\Factory::create();

        $password = \Illuminate\Support\Facades\Hash::make('voting');

        for ($i = 0; $i < 5; $i++) {
            \App\User::create([
                'name' => $faker->name,
                'email' => $faker->email,
                'password' => $password,
                'NIN' => $faker->unique()->randomDigit,
            ]);
        }
    }
}
