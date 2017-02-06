<?php

use Illuminate\Database\Seeder;

class EventosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('eventos')->insert(array('id' => '1','name'  => 'ACD',  			     'estado_call_id'   => '1',  'estado_visible_id'   => '1', 'eventos_auxiliares' => '0', 'cosapi_eventos' => '0', 					'claro_eventos' => '0', 'estado_id' => '1' ));
        \DB::table('eventos')->insert(array('id' => '2','name'  => 'Break',  			 'estado_call_id'   => '2',  'estado_visible_id'   => '1', 'eventos_auxiliares' => '1', 'cosapi_eventos' => '0',					'claro_eventos' => '0', 'estado_id' => '1' ));
        \DB::table('eventos')->insert(array('id' => '3','name'  => 'SS.HH',  			 'estado_call_id'   => '2',  'estado_visible_id'   => '1', 'eventos_auxiliares' => '1', 'cosapi_eventos' => '0',					'claro_eventos' => '0', 'estado_id' => '1' ));
        \DB::table('eventos')->insert(array('id' => '4','name'  => 'Refrigerio', 		 'estado_call_id'   => '2',  'estado_visible_id'   => '1', 'eventos_auxiliares' => '1', 'cosapi_eventos' => '0',					'claro_eventos' => '0', 'estado_id' => '1' ));
        \DB::table('eventos')->insert(array('id' => '5','name'  => 'Feedback',  		 'estado_call_id'   => '2',  'estado_visible_id'   => '1', 'eventos_auxiliares' => '1', 'cosapi_eventos' => '0',					'claro_eventos' => '0', 'estado_id' => '1' ));
        \DB::table('eventos')->insert(array('id' => '6','name'  => 'Capacitación',		 'estado_call_id'   => '2',  'estado_visible_id'   => '1', 'eventos_auxiliares' => '1', 'cosapi_eventos' => '0',					'claro_eventos' => '0', 'estado_id' => '1' ));
        \DB::table('eventos')->insert(array('id' => '7','name'  => 'Gestión BackOffice', 'estado_call_id'   => '2',  'estado_visible_id'   => '1', 'eventos_auxiliares' => '1', 'cosapi_eventos' => '1',					'claro_eventos' => '0', 'estado_id' => '1' ));
        \DB::table('eventos')->insert(array('id' => '8','name'  => 'Inbound',  		     'estado_call_id'   => '2',  'estado_visible_id'   => '2', 'eventos_auxiliares' => '0', 'cosapi_eventos' => '1',					'claro_eventos' => '1', 'estado_id' => '1' ));
        \DB::table('eventos')->insert(array('id' => '9','name'  => 'OutBound',  		 'estado_call_id'   => '2',  'estado_visible_id'   => '2', 'eventos_auxiliares' => '0', 'cosapi_eventos' => '1',					'claro_eventos' => '1', 'estado_id' => '1' ));
        \DB::table('eventos')->insert(array('id' => '10','name' => 'ACW',  			     'estado_call_id'   => '2',  'estado_visible_id'   => '2', 'eventos_auxiliares' => '0', 'cosapi_eventos' => '1',					'claro_eventos' => '0', 'estado_id' => '1' ));
        \DB::table('eventos')->insert(array('id' => '11','name' => 'Login',  			 'estado_call_id'   => '2',  'estado_visible_id'   => '2', 'eventos_auxiliares' => '0', 'cosapi_eventos' => '0',					'claro_eventos' => '0', 'estado_id' => '1' ));
        \DB::table('eventos')->insert(array('id' => '12','name' => 'Estado B',  		 'estado_call_id'   => '2',  'estado_visible_id'   => '2', 'eventos_auxiliares' => '0', 'cosapi_eventos' => '0',					'claro_eventos' => '0', 'estado_id' => '1' ));
        \DB::table('eventos')->insert(array('id' => '13','name' => 'Estado C',  		 'estado_call_id'   => '2',  'estado_visible_id'   => '2', 'eventos_auxiliares' => '0', 'cosapi_eventos' => '0',					'claro_eventos' => '0', 'estado_id' => '1' ));
        \DB::table('eventos')->insert(array('id' => '14','name' => 'Estado D',  		 'estado_call_id'   => '2',  'estado_visible_id'   => '2', 'eventos_auxiliares' => '0', 'cosapi_eventos' => '0',					'claro_eventos' => '0', 'estado_id' => '1' ));
        \DB::table('eventos')->insert(array('id' => '15','name' => 'Desconectado',  	 'estado_call_id'   => '2',  'estado_visible_id'   => '2', 'eventos_auxiliares' => '0', 'cosapi_eventos' => '0',					'claro_eventos' => '0', 'estado_id' => '1' ));
       
    }
}


