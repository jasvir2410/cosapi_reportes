<?php

namespace Cosapi\Http\Controllers;

use Illuminate\Http\Request;

use Cosapi\Http\Requests;
use Cosapi\Http\Controllers\Controller;

class ExcelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('elements/excel/index');
    }

    public function resultado(Request $request)
    {
        
        //dd($request);

        $nombre = $request->txtNombre;
        $paterno ='HOLA2';
        $materno ='HOLA3';
        return View('elements/excel/resultado')->with(array(
            'nombre'   => $nombre,
            'paterno'   => $paterno,
            'materno'   => $materno
            ));
    }

}
