<?php

use Illuminate\Database\Seeder;

class QueuesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('queues')->insert(array('id'  =>  '1', 'name'   =>  'Soporte_Interno', 'vdn' =>  '89888', 'queues_strategy_id' => '1', 'queues_priority_id' => '1', 'estado_id' =>  '1' ));

        \DB::table('queues')->insert(array('id'  =>  '2', 'name'   =>  'Soporte_Externo', 'vdn' =>  '89777', 'queues_strategy_id' => '1', 'queues_priority_id' => '1',        'estado_id' =>  '1' ));
    }
}

