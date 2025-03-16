<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Marca;

class MarcaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Query para consultar clientes
            $marca = Marca::all();
            if ($marca->count()>0) {
                // Si hay clientes se retorna el listado en un json
                return response()->json([
                    'code' => 200,
                    'data' => $marca
                ], 200);
            } else {
                // Si hay clientes se un mensaje
                return response()->json([
                    'code' => 404,
                    'data' => 'No hay marcas'
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
            $marca = Marca::create($request->all());
           
            return response()->json([
                'code' => 200,
                'data' => 'Marca insertada'
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
            $marca = Marca::find($id);
            if($marca){
                // Si el cliente existe se retornan sus datos  
                $datos = Marca::select("id","nombre")
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
                    'data' => 'Marca no encontrada'
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
                'nombre' => 'required'
            ]);
           
            if($validacion->fails()){
                // Si no se cumple la validación se devuelve el mensaje de error
                return response()->json([
                'code' => 400,
                'data' => $validacion->messages()
                ], 400);
            } else {
                // Si se cumple la validación se busca el cliente
                $marca = Marca::find($id);
                if($marca){
                    // Si el cliente existe se actualiza
                    $marca->update($request->all());
                    return response()->json([
                        'code' => 200,
                        'data' => 'Marca actualizada'
                    ], 200);
                } else {
                    // Si el cliente no existe se devuelve un mensaje
                    return response()->json([
                        'code' => 404,
                        'data' => 'Marca no encontrada'
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
            $marca = Marca::find($id);
            if($marca){
                // Si el cliente existe se elimina
                $marca->delete($id);
                return response()->json([
                    'code' => 200,
                    'data' => 'Marca eliminada'
                ], 200);
            } else {
                // Si el cliente no existe se devuelve un mensaje
                return response()->json([
                    'code' => 404,
                    'data' => 'Marca no encontrada'
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
       }
    }
}
