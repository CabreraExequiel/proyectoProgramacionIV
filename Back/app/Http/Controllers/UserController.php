<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }
    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Crear un nuevo usuario",
     *     tags={"Usuarios"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="role", type="string", example="usuario")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Usuario creado correctamente")
     * )
     */
    public function store(Request $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role ?? 'usuario', // rol por defecto: usuario
        ]);

        return response()->json(['message' => 'Usuario creado correctamente'], 201);
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     summary="Actualizar un usuario existente",
     *     tags={"Usuarios"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="role", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Usuario actualizado correctamente")
     * )
     */
    public function update(Request $request, User $user)
    {
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role ?? $user->role, // mantener rol actual si no se envía
        ]);

        return response()->json([
        'message' => 'Usuario actualizado correctamente',
        'user' => $user
    ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Eliminar un usuario",
     *     tags={"Usuarios"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Usuario eliminado correctamente")
     * )
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json([
        'message' => 'Usuario eliminado correctamente',
        'user_id' => $user->id
    ]);
    }


    /**
     * @OA\Get(
     *     path="/api/usuarios-registrados",
     * 
     *     summary="Obtener lista de usuarios registrados",
     *     tags={"Usuarios"},
     *     @OA\Response(response=200, description="Lista de usuarios")
     * )
     */
    public function getUsuariosRegistrados()
    {
        $usuarios = User::select('id', 'name', 'email', 'created_at')->get();

        return response()->json($usuarios);
    }
}
