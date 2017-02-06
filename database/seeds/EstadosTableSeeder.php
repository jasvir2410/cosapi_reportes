<?php

use Illuminate\Database\Seeder;

class EstadosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       \DB::table('estados')->insert(array('id'  =>  '1', 'name'  =>  'Activo'    ));
        \DB::table('estados')->insert(array('id'  =>  '2', 'name'  =>  'Inactivo'  ));
    }
}
