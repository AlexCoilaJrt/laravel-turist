<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            'email' => 'required|email',
        ], [
            'email.required' => 'El campo correo es obligatorio',
            'email.email' => 'El correo no tiene el formato correcto',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $credentials = request(['email', 'password']);
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Datos de acceso incorrectos. Por favor, verifica tus credenciales.'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->respondWithToken($token);
    }

    public function unauthorized()
    {
        return redirect(route('login'));
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|min:2',
            'password' => 'required|string|max:255|min:8',
            'email' => 'required|email|max:100|unique:users,email',
            'rol' => 'required|string|exists:roles,name',
        ], [
            'name.required' => 'El campo nombre es obligatorio',
            'name.min' => 'El nombre debe tener al menos :min caracteres',
            'name.max' => 'El nombre no puede tener más de :max caracteres',
            'email.required' => 'El campo correo es obligatorio',
            'email.unique' => 'El correo ya está registrado',
            'email.email' => 'El correo no tiene el formato correcto',
            'password.required' => 'El campo contraseña es obligatorio',
            'password.min' => 'El campo contraseña debe tener mínimo :min caracteres',
            'rol.required' => 'El rol es obligatorio',
            'rol.exists' => 'El rol ingresado no es válido',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $user = User::create([
            'name' => htmlspecialchars($request->input('name')),
            'email' => htmlspecialchars($request->input('email')),
            'password' => Hash::make($request->input('password')),
            'empresa_id' => null
        ]);

        $user->assignRole($request->rol); // ← Asignar rol de Spatie al usuario

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'user' => $user->load('roles'),
        ], Response::HTTP_CREATED);
    }

    public function me()
    {
        return response()->json(auth()->user()->load('roles'));
    }

    public function logout()
    {
        auth()->logout();
        try {
            $token = JWTAuth::getToken();
            if (!$token) {
                return response()->json(['error' => 'Token no encontrado'], Response::HTTP_BAD_REQUEST);
            }

            JWTAuth::invalidate($token);
            return response()->json(['message' => 'Sesión cerrada correctamente'], Response::HTTP_OK);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token inválido'], Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo cerrar la sesión'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function refresh()
    {
        try {
            $token = JWTAuth::getToken();
            if (!$token) {
                return response()->json(['error' => 'Token no encontrado'], Response::HTTP_BAD_REQUEST);
            }

            $nuevo_token = auth()->refresh();
            JWTAuth::invalidate($token);
            return $this->respondWithToken($nuevo_token);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token inválido'], Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo refrescar el token'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ], Response::HTTP_OK);
    }
}
