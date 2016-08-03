<?php

namespace Cosapi\Http\Controllers;

use Cosapi\Collector\Collector;
use Illuminate\Http\Request;

use Cosapi\Models\User;
use Cosapi\Models\Eventos;
use Cosapi\Models\AsteriskCDR;
use Cosapi\Models\DetalleEventos;

use Cosapi\Http\Requests;
use Illuminate\Support\Facades\DB;
use Cosapi\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;


class EventsAgentController extends Controller
{
    public function index(){
        return view('elements/events_detail/detail_events');
    }

    public function listar_estado_agentes (Request $request, $fecha_evento)
    {
        list($fecha_inicial,$fecha_final) = explode(' - ', $fecha_evento);

        $AsteriskCDR        =  AsteriskCDR::Select('accountcode as username' , DB::raw('SUM(billsec) as TiempoLlamada') )
                                    ->whereBetween(DB::raw("DATE(calldate)"),[$fecha_inicial, $fecha_final])
                                    ->where('disposition','=','ANSWERED')
                                    ->WHERE('lastapp','=','Dial')
                                    ->groupBy('accountcode')
                                    ->get()->toArray();

        $Usuarios           = User::get()->toArray();
        $Eventos            = Eventos::Select()->where('estado_id','=','1')->get();

        $Eventos_Auxiliares = Eventos::Select('id')->where('eventos_auxiliares','=','1' )->get();
        $Cosapi_Eventos     = Eventos::Select('id')->where('cosapi_eventos','=','1' )->get();
        $Claro_Eventos      = Eventos::Select('id')->where('claro_eventos','=','1' )->get();

        $DetalleEventos     = DetalleEventos::Select()
                                    ->with('evento')
                                    ->whereBetween(DB::raw("DATE(fecha_evento)"),[$fecha_inicial, $fecha_final])
                                    ->OrderBy('user_id', 'asc')
                                    ->OrderBy('fecha_evento', 'asc')
                                    ->get()->toArray();

        return View('elements/events_detail/detalle-estados-agentes')->with(array(
            'usuarios'          =>  $Usuarios,
            'eventos'           =>  $Eventos,
            'detalleEventos'    =>  $DetalleEventos,
            'AsteriskCDR'       =>  $AsteriskCDR,
            'formato'           =>  false
            )
        ) ;
    }

    public function events_detail (Request $request){
        if ($request->ajax()){
            if ($request->fecha_evento){

                $days                   = explode(' - ',$request->fecha_evento);
                $query_events           = $this->query_events($days);
                $array_detail_events    = detailEvents($query_events);
                $objCollection          = $this->convertCollection($array_detail_events);
                $detail_events          = $this->format_datatable($objCollection);
                return $detail_events;

            }else{
                return view('elements/events_detail/index');
            }
        }
    }

    protected function query_events($days){

        $detail_events     = DetalleEventos::Select()
                                ->with('evento','user')
                                ->filtro_days($days)
                                ->OrderBy(DB::raw('user_id'), 'asc')
                                ->OrderBy(DB::raw('DATE(fecha_evento)'), 'asc')
                                ->OrderBy(DB::raw('TIME(fecha_evento)'), 'asc')
                                ->get();

        return $detail_events;
    }

    protected function format_datatable($arrays){
        $format_datatable  = Datatables::of($arrays)->make(true);
        return $format_datatable;
    }

    function convertCollection($array){

        $objCollection = New Collector();
        foreach ($array as $data) {
            $objCollection->push([
                'nombre_agente'     => $data['full_name_user'],
                'fecha'             => $this->MostrarSoloFecha($data['fecha_evento']),
                'hora'              => $this->MostrarSoloHora($data['fecha_evento']),
                'evento'            => $data['name_evento'],
                'accion'            => $data['accion'],
            ]);
        }

        return $objCollection;
    }

}
