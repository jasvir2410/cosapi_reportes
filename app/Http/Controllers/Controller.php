<?php

namespace Cosapi\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected  function MostrarFechaActual(){
        $date = Carbon::now();
        $date = $date->format('Y-m-d');
        return $date;
    }

    protected  function MostrarSoloHora($fecha){
        $date='00:00:00';
        if( ! empty($fecha)){
            $date = Carbon::parse($fecha);
            $date = $date->toTimeString();
        }
        return $date;
    }

    protected  function MostrarSoloFecha($fecha){
        if( ! empty($fecha)){
            $date = Carbon::parse($fecha);
            $date = $date->toDateString();
            return $date;
        }
    }
}
