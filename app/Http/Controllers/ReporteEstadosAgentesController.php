<?php

namespace Cosapi\Http\Controllers;

use Illuminate\Http\Request;

use Cosapi\Models\AsteriskCDR;
use Cosapi\Models\User;
use Cosapi\Models\DetalleEventos;
use Cosapi\Models\Eventos;

use Cosapi\Http\Requests;
use Cosapi\Http\Controllers\Controller;
use DB;// para hacer las consultas a la base de datos sin necesidad de refenenciar le modelo


class ReporteEstadosAgentesController extends Controller
{
    public function index()
    {
        return view('elements/Reporte_Estados_Agentes/index');
    }

    /********buscador por fecha para reporte estados-agentes******/
    public function listar_estado_agentes (Request $request, $fecha_evento)
    {
        //Para separar la fecha de un daterange
        list($fecha_inicial,$fecha_final)=explode(' - ', $fecha_evento);

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

        return View('elements/Reporte_Estados_Agentes/detalle-estados-agentes')->with(array(
            'usuarios'          =>  $Usuarios,
            'eventos'           =>  $Eventos,
            'detalleEventos'    =>  $DetalleEventos,
            'AsteriskCDR'       =>  $AsteriskCDR,
            'formato'           =>  false
            )
        ) ;
    }

}
