<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Query para consultar usuarios
            $users = User::all();
            if ($users->count() > 0) {
                // Si hay usuarios se retorna el listado en un json
                return response()->json([
                    'code' => 200,
                    'data' => $users
                ], 200);
            } else {
                // Si no hay usuarios se retorna un mensaje
                return response()->json([
                    'code' => 404,
                    'data' => 'No hay usuarios'
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Se valida que todos los campos sean requeridos
            $validacion = Validator::make($request->all(), [
                'name' => 'required',
                'rol' => 'required',
                'email' => 'required|email',
                'password' => 'required'
            ]);
    
            if ($validacion->fails()) {
                // Si no se cumple la validación se devuelve el mensaje de error
                return response()->json([
                    'code' => 400,
                    'data' => $validacion->messages()
                ], 400);
            } else {
                // Se crea el usuario
                $user = User::create($request->all());
    
                return response()->json([
                    'code' => 200,
                    'data' => 'Usuario insertado'
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Se busca el usuario
            $user = User::find($id);
            if ($user) {
                // Si el usuario existe se retornan sus datos
                $datos = User::select("id", "name", 'rol',"email")
                    ->where("id", "=", $id)
                    ->get();
                return response()->json([
                    'code' => 200,
                    'data' => $datos[0]
                ], 200);
            } else {
                // Si el usuario no existe se devuelve un mensaje
                return response()->json([
                    'code' => 404,
                    'data' => 'Usuario no encontrado'
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Se valida que todos los campos sean requeridos
            $validacion = Validator::make($request->all(), [
                'name' => 'required',
                'rol' => 'required',
                'email' => 'required|email',
                'password' => 'required'
            ]);
    
            if ($validacion->fails()) {
                // Si no se cumple la validación se devuelve el mensaje de error
                return response()->json([
                    'code' => 400,
                    'data' => $validacion->messages()
                ], 400);
            } else {
                // Se busca el usuario
                $user = User::find($id);
                if ($user) {
                    // Si el usuario existe se actualiza
                    $user->update($request->all());
                    return response()->json([
                        'code' => 200,
                        'data' => 'Usuario actualizado'
                    ], 200);
                } else {
                    // Si el usuario no existe se devuelve un mensaje
                    return response()->json([
                        'code' => 404,
                        'data' => 'Usuario no encontrado'
                    ], 404);
                }
            }
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Se busca el usuario
            $user = User::find($id);
            if ($user) {
                // Si el usuario existe se elimina
                $user->delete();
                return response()->json([
                    'code' => 200,
                    'data' => 'Usuario eliminado'
                ], 200);
            } else {
                // Si el usuario no existe se devuelve un mensaje
                return response()->json([
                    'code' => 404,
                    'data' => 'Usuario no encontrado'
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }

    public function login(Request $request){
        try {
            $validacion = Validator::make($request->all(),[
                'email' => 'required',
                'password' => 'required'
            ]);
            
            if($validacion->fails()){
                return response()->json([
                    'code' => 400,
                    'data' => $validacion->messages()
                ], 400);
            } else {
                if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
                    $usuario = User::where('email', $request->email)->first();
                    return response()->json([
                        'code' => 200,
                        'data' => [
                            'usuario' => $usuario->name,  // Nombre
                            'id' => $usuario->id,         // ID
                            'rol' => $usuario->rol,       // Rol
                            'email' => $usuario->email    // Email
                        ],
                        'token' => $usuario->createToken('api-key')->plainTextToken
                    ], 200);
                } else {
                    return response()->json([
                        'code' => 401,
                        'data' => 'Usuario no autorizado',
                    ], 401);
                }
            }
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }
 
    public function register(Request $request){
        try {
        // Se validan los campos que se reciben
            $validacion = Validator::make($request->all(), [
                'id' => '',
                'name' => 'required',
                'email' => 'required|unique:users',
                'password' => 'required',
            ]);
            if($validacion->fails()){
            // Si la validación no se cumple, se retornan los mensajes de error
                    return response()->json([
                    'code' => 400,
                    'data' => $validacion->messages()
                ], 400);
            } else {
            // Si la validación no falla, se crea el usuario y se retorna la respuesta
                $usuario = User::create($request->all());
                return response()->json([
                    'code' => 200,
                    // Se retornan los datos del usuario creado
                    'data' => $usuario,
                    // Se crea un token para el usuario creado
                    'token' => $usuario->createToken('api-key')->plainTextToken
            ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }
}