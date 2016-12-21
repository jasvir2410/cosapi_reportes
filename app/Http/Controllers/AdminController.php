<?php

namespace Cosapi\Http\Controllers;

use Illuminate\Http\Request;

use Cosapi\Http\Requests;
use Cosapi\Http\Controllers\Controller;

class AdminController extends Controller
{
	/**
	 * [index Función que carga la base del AdminLTE]
	 * @return [view] [Returna la vista base del Admin LTE]
	 */
    public function index()
    {
        return view('/front-admin');
    }
}
