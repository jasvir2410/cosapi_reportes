<?php

use Illuminate\Database\Seeder;

class AgentesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('agentes')->insert(array('id'  =>  '1',   'name'   =>  '251', 'estado_id' =>  '1' ));
        \DB::table('agentes')->insert(array('id'  =>  '2',   'name'   =>  '252', 'estado_id' =>  '1' ));
        \DB::table('agentes')->insert(array('id'  =>  '3',   'name'   =>  '253', 'estado_id' =>  '1' ));
        \DB::table('agentes')->insert(array('id'  =>  '4',   'name'   =>  '254', 'estado_id' =>  '1' ));
    }
}
