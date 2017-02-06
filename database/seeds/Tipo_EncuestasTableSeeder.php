<?php

use Illuminate\Database\Seeder;

class Tipo_EncuestasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('tipo_encuestas')->insert(array('id'  =>  '1', 'name'   =>  'Entrante', 'estado_id' =>  '1' ));

        \DB::table('tipo_encuestas')->insert(array('id'  =>  '2', 'name'   =>  'Saliente', 'estado_id' =>  '1' ));

        \DB::table('tipo_encuestas')->insert(array('id'  =>  '3', 'name'   =>  'Liberada', 'estado_id' =>  '1' ));
    }
}
