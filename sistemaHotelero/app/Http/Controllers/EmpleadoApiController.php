<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleados;
use App\Models\Usuarios;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class EmpleadoApiController extends Controller
{
    public function listarEmpleados()
    {
        $empleados = Empleados::with(['usuario.rol'])->get();
        return response()->json($empleados);
    }

    public function listarRoles()
    {
        $roles = Rol::all();
        return response()->json($roles);
    }

    public function crearEmpleado(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'ap' => 'required|string|max:50',
            'am' => 'required|string|max:50',
            'telefono' => 'nullable|string|max:15',
            'email' => 'required|email|unique:empleados,email',
            'username' => 'required|string|unique:usuarios,username',
            'password' => 'required|string|min:6',
            'id_rol' => 'required|exists:roles,id_rol'
        ]);

        try {
            DB::beginTransaction();

            $empleado = Empleados::create([
                'nombre' => $request->nombre,
                'ap' => $request->ap,
                'am' => $request->am,
                'telefono' => $request->telefono,
                'email' => $request->email,
            ]);

            Usuarios::create([
                'username' => $request->username,
                'contrasena' => Hash::make($request->password),
                'id_rol' => $request->id_rol,
                'id_empleado' => $empleado->id_empleado,
            ]);

            DB::commit();
            return response()->json(['mensaje' => 'Empleado y usuario creados con éxito', 'empleado' => $empleado], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['mensaje' => 'Error al crear empleado: ' . $e->getMessage()], 500);
        }
    }

    public function actualizarEmpleado(Request $request, $id)
    {
        $empleado = Empleados::findOrFail($id);
        $usuario = Usuarios::where('id_empleado', $id)->first();

        $request->validate([
            'nombre' => 'required|string|max:50',
            'ap' => 'required|string|max:50',
            'am' => 'required|string|max:50',
            'telefono' => 'nullable|string|max:15',
            'email' => 'required|email|unique:empleados,email,' . $id . ',id_empleado',
            'username' => 'required|string|unique:usuarios,username,' . ($usuario ? $usuario->id_usuario : 'NULL') . ',id_usuario',
            'id_rol' => 'required|exists:roles,id_rol'
        ]);

        try {
            DB::beginTransaction();

            $empleado->update($request->only(['nombre', 'ap', 'am', 'telefono', 'email']));

            if ($usuario) {
                $usuario->username = $request->username;
                $usuario->id_rol = $request->id_rol;
                if ($request->filled('password')) {
                    $usuario->contrasena = Hash::make($request->password);
                }
                $usuario->save();
            }

            DB::commit();
            return response()->json(['mensaje' => 'Empleado actualizado con éxito']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['mensaje' => 'Error al actualizar empleado: ' . $e->getMessage()], 500);
        }
    }

    public function eliminarEmpleado($id)
    {
        try {
            DB::beginTransaction();
            
            // Eliminar usuario asociado primero por integridad (aunque la BD tenga FK)
            Usuarios::where('id_empleado', $id)->delete();
            Empleados::destroy($id);

            DB::commit();
            return response()->json(['mensaje' => 'Empleado eliminado con éxito']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['mensaje' => 'Error al eliminar empleado: ' . $e->getMessage()], 500);
        }
    }
}
