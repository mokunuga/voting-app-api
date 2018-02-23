<?php

use Illuminate\Database\Seeder;

class CandidatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $post_ids = \App\Post::pluck('id')->all();
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 10; $i++) {
            \App\Candidate::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'post_id' => $faker->randomElement($post_ids),
                'manifesto' => $faker->realText(),
                'candidate_image' => $faker->imageUrl(),

            ]);
        }
    }
}
