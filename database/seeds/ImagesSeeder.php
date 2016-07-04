<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Faker\Factory as Faker;

class ImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        for($i=0;$i < 12;$i++) {
            \DB::table('images')->insert(array(
                'imagesUrl' => $faker->imageUrl($width = 171, $height = 180, 'nature'),
            ));
        }
    }
}
