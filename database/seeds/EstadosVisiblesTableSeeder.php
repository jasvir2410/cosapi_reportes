<?php

use Illuminate\Database\Seeder;

class EstadosVisiblesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('estados_visibles')->insert(array('id'  =>  '1', 'name'  =>  'Mostrar'  ));
        \DB::table('estados_visibles')->insert(array('id'  =>  '2', 'name'  =>  'Ocultar'  ));
    }
}
