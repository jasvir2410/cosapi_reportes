<?php

namespace Cosapi\Http\Controllers;

use Illuminate\Http\Request;

use Cosapi\Http\Requests;
use Cosapi\Http\Controllers\Controller;

use Datatables;
use Excel;
use Carbon\Carbon;

class CosapiController extends Controller
{

    
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


    protected function FormatDatatable($collection)
    {
        return Datatables::of($collection)->make(true);
    }


    protected function BuilderExport($array,$namefile,$format,$location){
        Excel::create($namefile, function($excel) use($array,$namefile) {

            $excel->sheet($namefile, function($sheet) use($array) {
                $sheet->fromArray($array);
            });

        })->store($format,$location);
    }

}