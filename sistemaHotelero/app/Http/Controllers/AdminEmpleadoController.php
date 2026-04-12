<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminEmpleadoController extends Controller
{
    public function index()
    {
        return view('admin.empleados');
    }
}
