<?php

use Illuminate\Database\Seeder;

class AnexosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('anexos')->insert(array('id'  =>  '1', 'name'   =>  '251', 'user_id' => '0', 'estado_id' =>  '1' ));
        \DB::table('anexos')->insert(array('id'  =>  '2', 'name'   =>  '252', 'user_id' => '0', 'estado_id' =>  '1' ));
        \DB::table('anexos')->insert(array('id'  =>  '3', 'name'   =>  '253', 'user_id' => '0', 'estado_id' =>  '1' ));
        \DB::table('anexos')->insert(array('id'  =>  '4', 'name'   =>  '254', 'user_id' => '0', 'estado_id' =>  '1' ));
    }
}
