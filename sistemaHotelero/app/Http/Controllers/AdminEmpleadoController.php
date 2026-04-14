<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminEmpleadoController extends Controller
{
    public function mostrarVistaEmpleados()
    {
        return view('admin.empleados');
    }
}
