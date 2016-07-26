<?php

namespace Cosapi\Http\Controllers;

use Cosapi\Http\Requests;
use Illuminate\Http\Request;
use Cosapi\Models\Cdr;
use Cosapi\Http\Controllers\Controller;

use DB;
use Datatables;
use Illuminate\Support\Facades\Log;

class ListarLlamadasSalientesController extends Controller
{
    public function index(Request $request)
    {        
        if ($request->ajax()){
            if ($request->fecha_evento){
                return $this->list_calls_outgoing($request->fecha_evento);
            }else{
                return view('elements/Listar_Llamadas_Salientes/index');
            }
        }
    }


    /**
     * [listar_llamadas_consolidadas Función para listar el consolidado de llamadas]
     * @param  Request   $request        [Dato para identifcar GET O POST]
     * @param  [string]  $fecha_evento   [Recibe el rango de fecha e buscar]
     * @return [Array]                   [Retorna la lista del consolidado de llamadas]
     */
    public function list_calls_outgoing($fecha_evento){
        
        $days                   = explode(' - ', $fecha_evento);
        $tamano_anexo           = array ('3','4');
        $tamano_telefono        = array ('5','6','7','9');
        $calls_outgoing         = $this->query_calls_outgoing($days, $tamano_anexo, $tamano_telefono);        
        $list_call_outgoing     = $this->format_datatable($calls_outgoing);

        return $list_call_outgoing;   
    }

    protected function query_calls_outgoing($days, $tamano_anexo, $tamano_telefono){
        $query_calls_outgoing=Cdr::Select()
                                    ->whereIn(DB::raw('LENGTH(dst)'),$tamano_telefono)
                                    ->where('dst','not like','*%')
                                    ->where('disposition','=','ANSWERED')
                                    ->filtro_days($days)
                                    ->OrderBy('src')
                                    ->get();

        return $query_calls_outgoing;
    }


    protected function format_datatable($arrays)
    {
        $format_datatable  = Datatables::of($arrays)
                                    ->addColumn('date', function ($array) {
                                            return date('d-m-Y',strtotime($array->calldate));
                                        })
                                    ->addColumn('hour', function ($array) {
                                            return date('H:i:s',strtotime($array->calldate));
                                        })
                                    ->make(true);
        return $format_datatable;
    }

}
