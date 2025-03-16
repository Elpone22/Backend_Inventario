<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categorias = Categoria::all();
            if ($categorias->count() > 0) {
                return response()->json([
                    'code' => 200,
                    'data' => $categorias
                ], 200);
            } else {
                return response()->json([
                    'code' => 404,
                    'data' => 'No hay categorías disponibles'
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
                'nombre' => 'required'
            ]);
           
            if($validacion->fails()){
                // Si no se cumple la validación se devuelve el mensaje de error
                return response()->json([
                    'code' => 400,
                    'data' => $validacion->messages()
                ], 400);
            } else {
            // Si se cumple la validación se inserta el cliente
            $categoria = Categoria::create($request->all());
           
            return response()->json([
                'code' => 200,
                'data' => 'Categoria insertado'
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
            // Se busca el cliente
            $categoria = Categoria::find($id);
            if($categoria){
                // Si el cliente existe se retornan sus datos  
                $datos = Categoria::select("id","nombre")
                ->where("id","=",$id)
                ->get();
                return response()->json([
                    'code' => 200,
                    'data' => $datos[0]
                ], 200);
            } else {
                // Si el cliente no existe se devuelve un mensaje
                return response()->json([
                    'code' => 404,
                    'data' => 'categoria no encontrada'
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
                'nombre' => 'required',
                'orden' => ''
            ]);
           
            if($validacion->fails()){
                // Si no se cumple la validación se devuelve el mensaje de error
                return response()->json([
                'code' => 400,
                'data' => $validacion->messages()
                ], 400);
            } else {
                // Si se cumple la validación se busca el cliente
                $categoria = Categoria::find($id);
                if($categoria){
                    // Si el cliente existe se actualiza
                    $categoria->update($request->all());
                    return response()->json([
                        'code' => 200,
                        'data' => 'Categoria actualizada'
                    ], 200);
                } else {
                    // Si el cliente no existe se devuelve un mensaje
                    return response()->json([
                        'code' => 404,
                        'data' => 'Categoria no encontrada'
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
            // Se busca el cliente
            $categoria = Categoria::find($id);
            if($categoria){
                // Si el cliente existe se elimina
                $categoria->delete($id);
                return response()->json([
                    'code' => 200,
                    'data' => 'Categoria eliminada'
                ], 200);
            } else {
                // Si el cliente no existe se devuelve un mensaje
                return response()->json([
                    'code' => 404,
                    'data' => 'Categoria no encontrada'
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
       }
    }
}
