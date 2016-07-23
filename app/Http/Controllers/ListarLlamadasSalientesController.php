<?php

namespace Cosapi\Http\Controllers;

use Illuminate\Http\Request;

use Cosapi\Http\Requests;
use Cosapi\Http\Controllers\Controller;
use Cosapi\Models\Cdr;
use DB;

class ListarLlamadasSalientesController extends Controller
{
    /**
     * [index Función que muestra la ventana principal del módulo ListarLlamadasSalientes]
     * @return [Array] [retorna los datos para la vista]
     */
    public function index()
    {
        return view('elements/Listar_Llamadas_Salientes/index');
    }


    /**
     * [listar_llamadas_consolidadas Función para listar el consolidado de llamadas]
     * @param  Request   $request        [Dato para identifcar GET O POST]
     * @param  [string]  $fecha_evento   [Recibe el rango de fecha e buscar]
     * @return [Array]                   [Retorna la lista del consolidado de llamadas]
     */
    public function listar_llamadas_salientes(Request $request, $fecha_evento){
        
        $days                               = explode(' - ', $fecha_evento);
        $tamano_anexo                       = array ('3','4');
        $tamano_telefono                    = array ('5','6','7','9');
        $list_call_outgoing                 = $this->calls_outgoing($days, $tamano_anexo, $tamano_telefono);


        return View('elements/Listar_Llamadas_Salientes/listar-llamadas-salientes')->with(array(
            'llamadasSalientes'   => $list_call_outgoing
            ));   
    }

    protected function query_calls_outgoing($days, $tamano_anexo, $tamano_telefono){
        $query_calls_outgoing=Cdr::Select()
                                    ->whereIn(DB::raw('LENGTH(dst)'),$tamano_telefono)
                                    ->where('dst','not like','*%')
                                    ->where('disposition','=','ANSWERED')
                                    ->filtro_days($days)
                                    ->OrderBy('src')
                                    ->get()
                                    ->toArray();

        
        return $query_calls_outgoing;
    }

    protected function calls_outgoing($days, $tamano_anexo, $tamano_telefono){

        
        $query_calls_outgoing   = $this->query_calls_outgoing($days, $tamano_anexo, $tamano_telefono);
        $calls_outgoing         = ListarLlamadasSalientes($query_calls_outgoing);

        return $calls_outgoing;
    }

}
