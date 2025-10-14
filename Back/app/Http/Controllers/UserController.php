<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Importado para el cifrado de contraseñas

class UserController extends Controller
{
    /**
     * Listar todos los usuarios (Solo Administrador)
     *
     * @OA\Get(
     * path="/api/users",
     * summary="Listar todos los usuarios (Solo Administrador)",
     * tags={"Usuarios"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(response=200, description="Lista de usuarios"),
     * @OA\Response(response=403, description="Acceso denegado (Requiere Administrador)")
     * )
     */
    public function index()
    {
        if (auth()->user() && auth()->user()->role !== 'administrador') {
            return response()->json(['message' => 'Acceso denegado. Se requiere rol de administrador.'], 403);
        }
        
        $users = User::select('id', 'name', 'email', 'role', 'created_at')->get();
        return response()->json($users);
    }
    
    /**
     * Obtener un usuario por ID (Solo Administrador)
     *
     * @OA\Get(
     * path="/api/users/{id}",
     * summary="Obtener un usuario por ID (Solo Administrador)",
     * tags={"Usuarios"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Datos del usuario"),
     * @OA\Response(response=403, description="Acceso denegado (Requiere Administrador)"),
     * @OA\Response(response=404, description="Usuario no encontrado")
     * )
     */
    public function show(User $user)
    {
        if (auth()->user() && auth()->user()->role !== 'administrador') {
            return response()->json(['message' => 'Acceso denegado. Se requiere rol de administrador.'], 403);
        }
        
        return response()->json($user->only(['id', 'name', 'email', 'role', 'created_at']));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'sometimes|string|in:usuario', 
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            // 🔒 Hashing obligatorio de la contraseña
            'password' => Hash::make($validated['password']),
            'role' => 'usuario', // Siempre forzar 'usuario' en la ruta de registro pública
        ]);

        return response()->json(['message' => 'Usuario registrado correctamente', 'user' => $user->only(['id', 'name', 'email'])], 201);
    }
    

    /**
     * @OA\Put(
     * path="/api/users/{id}",
     * summary="Actualizar un usuario existente (Solo Administrador)",
     * tags={"Usuarios"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="name", type="string"),
     * @OA\Property(property="email", type="string", format="email"),
     * @OA\Property(property="role", type="string", example="administrador") 
     * )
     * ),
     * @OA\Response(response=200, description="Usuario actualizado correctamente"),
     * @OA\Response(response=403, description="Acceso denegado (Requiere Administrador)")
     * )
     */
    public function update(Request $request, User $user)
    {
        if (auth()->user() && auth()->user()->role !== 'administrador') {
            return response()->json(['message' => 'Acceso denegado. Se requiere rol de administrador para modificar usuarios.'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            // Validación de email único, excluyendo al usuario actual
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|nullable|string|min:8',
            'role' => 'sometimes|required|string|in:usuario,administrador', // Actualizado
        ]);

        $data = $validated;
        
        // Hashing de contraseña si se proporciona
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']); // No actualizar si no se envía
        }
        
        $user->update($data);

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'user' => $user->only(['id', 'name', 'email', 'role'])
        ], 200);
    }

    /**
     * @OA\Delete(
     * path="/api/users/{id}",
     * summary="Eliminar un usuario (Solo Administrador)",
     * tags={"Usuarios"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Usuario eliminado correctamente"),
     * @OA\Response(response=403, description="Acceso denegado (Requiere Administrador)")
     * )
     */
    public function destroy(User $user)
    {
        if (auth()->user() && auth()->user()->role !== 'administrador') {
            return response()->json(['message' => 'Acceso denegado. Se requiere rol de administrador.'], 403);
        }

        if (auth()->id() === $user->id) {
            return response()->json(['message' => 'No puedes eliminar tu propia cuenta de administrador a través de esta ruta.'], 403);
        }

        $user->delete();
        
        return response()->json([
            'message' => 'Usuario eliminado correctamente',
            'user_id' => $user->id
        ], 200);
    }


    /**
     * @OA\Get(
     * path="/api/usuarios-registrados",
     * summary="Obtener lista de usuarios registrados (Solo Administrador)",
     * tags={"Usuarios"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(response=200, description="Lista de usuarios"),
     * @OA\Response(response=403, description="Acceso denegado (Requiere Administrador)")
     * )
     */
    public function getUsuariosRegistrados()
    {
        if (auth()->user() && auth()->user()->role !== 'administrador') {
            return response()->json(['message' => 'Acceso denegado. Se requiere rol de administrador.'], 403);
        }

        $usuarios = User::select('id', 'name', 'email', 'role', 'created_at')->get();

        return response()->json($usuarios);
    }
}
