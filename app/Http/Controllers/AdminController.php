<?php

namespace Cosapi\Http\Controllers;

use Illuminate\Http\Request;

use Cosapi\Http\Requests;
use Cosapi\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function index()
    {
        return view('/front-admin');
    }
}
