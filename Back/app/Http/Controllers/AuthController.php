<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validación
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255', //Se aclara el tipo de dato y cantidad ma´xima que acepta
            'email' => 'required|string|email|max:255|unique:users', //Aclara que dicho Email debe ser obligatorio y ademas es unico en la tabla "user"
            'password' => 'required|string|min:6|confirmed',
        ]);
        //Si falla devolvemos el error 422 (No procesable)
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Crea el usuario en la DB
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        //Sale todo bien entonces da codigo 201 :)
        return response()->json([
            'status' => 'success',
            'message' => 'Usuario registrado correctamente',
            'user' => $user
        ], 201);
    }
}
