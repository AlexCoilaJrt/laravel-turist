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

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function login(Request $request)
    {
     $validator=Validator::make($request->all(),[
        'password' => 'required|string',
        'email' => 'required|email',
     ],[
        'email.required' => 'El campo correo es obligatirio',
        'email.email' => 'El correo no tiene el formato correcto',
     ]);
     if ($validator->fails()) {
        return response()->json(['error'=>$validator->errors()], Response::HTTP_BAD_REQUEST);
     }
     $credentials = request(['email','password']);
     if (!$token=auth()->attempt($credentials)) {
        return response()->json(['error'=> 'Datos de accesos incorrectos. Por favor, verifica tus credencis'] , Response::HTTP_UNAUTHORIZED);
     }
     return $this ->respondWithToken($token);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function unauthorized()
    {
        return redirect(route('login'));

    }

   
    public function register(Request $request)
    {
        $validator=Validator::make($request->all(),[
           'name'=>'required|string|max:100|min:2',
           'password'=>'required|string|max:255|min:8',
           'email'=> 'required|email|max:100|unique:users,email',
        ],[
            'name.required'=>'El campo es obligatorio',
            'name.min'=>'en nombre debe tener almenos :min caracteres',
            'name.max'=>'El nombre no puede tener mas de :max caracteres',
            'email.required'=>'El campo correo es obligatorio',
            'email.unique'=>'El correo ya esta registrado',
            'email.email'=>'El correo no tiene el formato correcto',
            'password.required'=>'El campo contraseña es obligatorio',
            'pasword.min'=>'El campo contraseña es de minimo :min caracteres',

        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], Response::HTTP_BAD_REQUEST);
    }
    $exists =User::where('email', htmlspecialchars($request->input('email')))->first();
    if (!$exists) {
        $new=User::create([
            'name'=>htmlspecialchars($request->input('name')),
            'email'=> htmlspecialchars($request->input('email')),
            'password'=> Hash::make($request->input('password')),
            'rol'=>'CLIENTE',
            'empresa_id'=>null
        ]);
        if (! $new) {
            return response()->json(['error'=> 'No se logro crear'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json($new, Response::HTTP_CREATED);;
    }else {
            return response()->json(['error'=>'Ya existe un usuario con ese email'], Response::HTTP_BAD_REQUEST);
        }
    }
   

    public function me()
    {
        return response()->json(auth()->user());
    }

    
    public function logout()
    {
        auth()->logout();
        try {
            $token = JWTAuth::getToken();
            if (!$token) {
                return response()->json(['error'=> 'Token no encontrado'], Response::HTTP_BAD_REQUEST);
            }
            JWTAuth::invalidate($token);
            return response()->json(['message'=> 'Sesion cerrada correctamente'], Response::HTTP_OK);
        } catch (TokenInvalidException $e) {
            return response()->json(['error'=> 'Token Invalido'], Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return response()->json(['error'=> 'No se pudo cerrar la sesion'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
    public function refresh(){
        try {
            $token= JWTAuth::getToken();
            if (!$token) {
                return response()->json(['error'=> 'Token no encontrado'], Response::HTTP_BAD_REQUEST);
            }
            $nuevo_token=auth()->refresh();
            JWTAuth::invalidate($token);
            return $this->respondWithToken($nuevo_token);
        } catch (TokenInvalidException $e) {
            return response()->json(['error'=> 'Token invalido'], Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return response()->json(['error'=> 'No se pudo cerrar la sesion'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function respondWithToken($token){
        return response()->json([
            'access_token'=>$token,
            'token_type'=>'bearer',
            'expires_in'=>auth()->factory()->getTTL()*60

        ],Response::HTTP_OK);
}
}