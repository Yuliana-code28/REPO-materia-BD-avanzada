<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuarios;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Empleados;
use App\Models\Cliente;
use Illuminate\Support\Str;

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

    public function olvideMicontrasena(Request $request){
         
       $request->validate(['email' => 'required|email']);
       
       $Empleado = Empleados::where('email',$request->email)->first();

       $Cliente = Cliente::where('email',$request->email)->first();

       if(!$Empleado &&  !$Cliente){
           return response()->json(['Mensaje' => 'Usuario no registrado'],401);
       }

       $usuario = $Empleado 
            ? Usuarios::where('id_empleado', $Empleado->id_empleado)->first() 
            : Usuarios::where('id_cliente', $Cliente->id_cliente)->first();
       
       $token = Str::random(80);

       DB::table('password_resets')->updateOrInsert(
        ['email' => $request->email],
        ['token' => $token, 'created_at' => now()]
       );

       $link = url("/cambiar-contrasena?token=$token&email=".$request->email);
    
        Mail::raw("Recupera tu contraseña: $link", function($message) use ($request){
            $message->to($request->email)
                    ->subject("Recuperar contraseña");
        });
    
        return response()->json(['mensaje' => 'Revisa tu correo']);

    }


    public function cambiarContrasena(Request $request){

    
          $request->validate([
              'email' => 'required|email',
              'token' => 'required',
              'password' => 'required|confirmed|min:6'
          ]);
      
          $registro = DB::table('password_resets')
              ->where('email', $request->email)
              ->where('token', $request->token)
              ->first();
      
          if(!$registro){
              return response()->json(['mensaje' => 'Token inválido'], 400);
          }
      
          
          $empleado = Empleados::where('email', $request->email)->first();
          $cliente = Cliente::where('email', $request->email)->first();
      
          $usuario = $empleado 
              ? Usuarios::where('id_empleado', $empleado->id_empleado)->first()
              : Usuarios::where('id_cliente', $cliente->id_cliente)->first();
      
          $usuario->contrasena = Hash::make($request->password);
          $usuario->save();
      
         
          DB::table('password_resets')->where('email', $request->email)->delete();
      
          return response()->json(['mensaje' => 'Contraseña actualizada']);
}

}
