<?php

use Illuminate\Database\Seeder;

class Queues_prioritysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       \DB::table('queues_prioritys')->insert(array('id'  =>  '1', 'name'   =>  '10', 'description' => 'Prioridad 5', 'estado_id' =>  '1' ));
       \DB::table('queues_prioritys')->insert(array('id'  =>  '2', 'name'   =>  '20', 'description' => 'Prioridad 4', 'estado_id' =>  '1' ));
       \DB::table('queues_prioritys')->insert(array('id'  =>  '3', 'name'   =>  '30', 'description' => 'Prioridad 3', 'estado_id' =>  '1' ));
       \DB::table('queues_prioritys')->insert(array('id'  =>  '4', 'name'   =>  '40', 'description' => 'Prioridad 2', 'estado_id' =>  '1' ));
       \DB::table('queues_prioritys')->insert(array('id'  =>  '5', 'name'   =>  '50', 'description' => 'Prioridad 1', 'estado_id' =>  '1' ));
    }
}
