<?php

use Illuminate\Database\Seeder;

class EstadosCallsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('estados_calls')->insert(array('id'  =>  '1', 'name'    =>  'Activar Agente'    ));
        \DB::table('estados_calls')->insert(array('id'  =>  '2', 'name'    =>  'Pausar Agente'     ));    }
}
