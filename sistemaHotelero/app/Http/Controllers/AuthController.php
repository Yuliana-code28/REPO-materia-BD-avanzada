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
    public function registro(Request $request) {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'ap' => 'required|string|max:50',
            'am' => 'required|string|max:50',
            'email' => 'required|email|unique:clientes,email',
            'telefono' => 'required|string|max:20',
            'username' => 'required|string|unique:usuarios,username',
            'password' => 'required|string|min:6|confirmed'
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'ap.required' => 'El apellido paterno es obligatorio.',
            'am.required' => 'El apellido materno es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico es inválido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.unique' => 'El nombre de usuario ya está en uso.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.'
        ]);

        try {
            DB::beginTransaction();

            $cliente = Cliente::create([
                'nombre' => $request->nombre,
                'ap' => $request->ap,
                'am' => $request->am,
                'email' => $request->email,
                'telefono' => $request->telefono
            ]);

            Usuarios::create([
                'username' => $request->username,
                'contrasena' => Hash::make($request->password),
                'id_rol' => 3, // Rol de Cliente
                'id_cliente' => $cliente->id_cliente
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'mensaje' => 'Registro exitoso. Ahora puedes iniciar sesión.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al registrar: ' . $e->getMessage()
            ], 500);
        }
    }


    
    public function login(Request $request){
       
       $request->validate([
            'username' => 'required',
            'password' => 'required'
       ], [
            'username.required' => 'El nombre de usuario es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.'
       ]);

       $usuario = Usuarios::where('username',$request->username)->first();

       $valida = false;
       if ($usuario && $usuario->contrasena === $request->password) {
           // La contraseña que está en texto plano en la BD, se actualiza a Hash automáticamente
           $usuario->contrasena = Hash::make($request->password);
           $usuario->save();
           $valida = true;
       } else if ($usuario && Hash::check($request->password, $usuario->contrasena)) {
           $valida = true;
       }

       if(!$valida){
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
                $request->validate(['email' => 'required|email'], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico es inválido.'
        ]);
       
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
          ], [
              'email.required' => 'El correo electrónico es obligatorio.',
              'email.email' => 'El formato del correo electrónico es inválido.',
              'token.required' => 'El token es obligatorio.',
              'password.required' => 'La contraseña es obligatoria.',
              'password.confirmed' => 'La confirmación de la contraseña no coincide.',
              'password.min' => 'La contraseña debe tener al menos 6 caracteres.'
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

        function logout(Request $request){
             $request->user()->currentAccessToken()->delete();

             return response()->json([
                 'mensaje' => 'Sesión cerrada correctamente'
             ]);
        }

}
