<?php

use Illuminate\Database\Seeder;

class Queues_strategysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('queues_strategys')->insert(array('id'  =>  '1', 'name'   =>  'rrmemory', 	'estado_id' =>  '1' ));
        \DB::table('queues_strategys')->insert(array('id'  =>  '2', 'name'   =>  'ringall', 	'estado_id' =>  '1' ));
        \DB::table('queues_strategys')->insert(array('id'  =>  '3', 'name'   =>  'roundrobin',  'estado_id' =>  '1' ));
        \DB::table('queues_strategys')->insert(array('id'  =>  '4', 'name'   =>  'leastrecent', 'estado_id' =>  '1' ));
        \DB::table('queues_strategys')->insert(array('id'  =>  '5', 'name'   =>  'fewestcalls', 'estado_id' =>  '1' ));
        \DB::table('queues_strategys')->insert(array('id'  =>  '6', 'name'   =>  'random', 		'estado_id' =>  '1' ));
    }
}

