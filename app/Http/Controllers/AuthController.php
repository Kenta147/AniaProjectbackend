<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
public function login(Request $request)
    {
        // 1. Validar que vengan el email y password
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Buscar al usuario
        $user = User::where('email', $request->email)->first();

        // 3. Verificar si existe y si la contraseña es correcta
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Las credenciales son incorrectas.'
            ], 401);
        }

        // 4. Crear el token de acceso (Sanctum)
        $token = $user->createToken('auth_token')->plainTextToken;

        // 5. Responder a React con el token y datos del usuario
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'name' => $user->name,
                'role' => $user->role
            ]
        ]);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Cerraste sesión correctamente.'
        ]);
    }
   
}
