<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Registro de usuarios
     */
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Usuario registrado exitosamente',
                'user'    => $user,
                'token'   => $token,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al registrar usuario',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Login de usuarios
     */
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales no son válidas.'],
            ]);
        }

        $user  = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'user'    => $user,
            'token'   => $token,
        ], 200);
    }

    /**
     * Logout de usuarios
     */
    public function logout()
    {
        try {
            Auth::user()->tokens()->delete();

            return response()->json([
                'message' => 'Sesión cerrada correctamente',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al cerrar sesión',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
