<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuarios;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request){
       
       $request->validate([
            'username' => 'required',
            'password' => 'required'
       ]);

       $usuario = Usuarios::where('username',$request->username)->first();

       if(!$usuario || !Hash::check($request->password,$usuario->contrasena)){
           return response()->json(['mensaje'=> 'Credenciales Incorrectas'], 401);
       }
       
       $token = $usuario->createToken('api-token')->plainTextToken;
    
        return response()->json([
            'token' => $token,
            'message' => 'Login correcto',
            'usuario' => [
                'id' => $usuario->id_usuario,
                'username' => $usuario->username,
                'rol' => $usuario->rol->nombre_rol
            ]
        ]);

    }
}
