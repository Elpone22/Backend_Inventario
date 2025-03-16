<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use Illuminate\Support\Facades\Validator;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
{
    try {
        $productos = Producto::select(
            'productos.id', 'productos.nombre', 'productos.cantidad', 'productos.descripcion',
            'productos.precio', 'marcas.nombre as marca', 'categorias.nombre as categoria', 'productos.imagen'
        )
        ->join('marcas', 'productos.fk_marca', '=', 'marcas.id')
        ->join('categorias', 'productos.fk_categoria', '=', 'categorias.id')
        ->get();

        if ($productos->count() > 0) {
            // Convertir el nombre de la imagen en una URL completa
            $productos->transform(function ($producto) {
                if ($producto->imagen) {
                    $producto->imagen = url('storage/' . $producto->imagen);
                }
                return $producto;
            });

            return response()->json([
                'code' => 200,
                'data' => $productos
            ], 200);
        } else {
            return response()->json([
                'code' => 404,
                'data' => 'No hay productos'
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
        // Validación de los datos, incluyendo la imagen
        $validacion = Validator::make($request->all(), [
            'nombre' => 'required',
            'cantidad' => 'required',
            'descripcion' => 'required',
            'precio' => 'required',
            'fk_marca' => 'required',
            'fk_categoria' => 'required',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validacion->fails()) {
            return response()->json([
                'code' => 400,
                'data' => $validacion->messages()
            ], 400);
        }

        // Manejo de la imagen
        $imagenNombre = null;
        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen');
            $imagenNombre = time() . '_' . $imagen->getClientOriginalName();
            $imagen->move(public_path('/storage'), $imagenNombre);
        }

        // Creación del producto
        $producto = Producto::create(array_merge($request->all(), ['imagen' => $imagenNombre]));

        return response()->json([
            'code' => 200,
            'data' => 'Producto insertado'
        ], 200);
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
            $producto = Producto::find($id);
            if($producto){
                // Si el cliente existe se retornan sus datos  
                $datos = Producto::select('productos.id','productos.nombre', 'productos.cantidad', 'productos.descripcion',
                'productos.precio','marcas.nombre as marca','categorias.nombre as categoria', 'productos.imagen',
                'productos.fk_marca','productos.fk_categoria')
                
                ->join('marcas', 'productos.fk_marca', '=', 'marcas.id')
                ->join('categorias', 'productos.fk_categoria', '=', 'categorias.id')
                ->where('productos.id','=',$id)
                ->get();
                return response()->json([
                    'code' => 200,
                    'data' => $datos[0]
                ], 200);
            } else {
                // Si el cliente no existe se devuelve un mensaje
                return response()->json([
                    'code' => 404,
                    'data' => 'Producto no encontrado'
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
            $producto = Producto::find($id);
    
            if (!$producto) {
                return response()->json([
                    'code' => 404,
                    'data' => 'Producto no encontrado'
                ], 404);
            }
    
            // Validar los datos
            $validacion = Validator::make($request->all(), [
                'nombre' => 'required',
                'cantidad' => 'required',
                'descripcion' => 'required',
                'precio' => 'required',
                'fk_marca' => 'required',
                'fk_categoria' => 'required',
                'imagen' => 'nullable'
            ]);
    
            if ($validacion->fails()) {
                return response()->json([
                    'code' => 400,
                    'data' => $validacion->messages()
                ], 400);
            }
    
            // Manejo de la imagen si se sube una nueva
            if ($request->hasFile('imagen')) {
                // Eliminar la imagen anterior si existe
                if ($producto->imagen && file_exists(public_path('imagenes/productos/' . $producto->imagen))) {
                    unlink(public_path('imagenes/productos/' . $producto->imagen));
                }
    
                // Guardar la nueva imagen
                $imagen = $request->file('imagen');
                $imagenNombre = time() . '_' . $imagen->getClientOriginalName();
                $imagen->move(public_path('imagenes/productos'), $imagenNombre);
    
                $producto->imagen = $imagenNombre;
            }
    
            // Actualizar los datos del producto
            $producto->update($request->except('imagen'));
            $producto->save();
    
            return response()->json([
                'code' => 200,
                'data' => 'Producto actualizado'
            ], 200);
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
            // Buscar el producto por su ID
            $producto = Producto::find($id);
    
            if ($producto) {
                // Si el producto existe, eliminar la imagen asociada si existe
                if ($producto->imagen && file_exists(public_path('storage/' . $producto->imagen))) {
                    unlink(public_path('storage/' . $producto->imagen));
                }
    
                // Eliminar el producto de la base de datos
                $producto->delete();
    
                return response()->json([
                    'code' => 200,
                    'data' => 'Producto eliminado'
                ], 200);
            } else {
                // Si el producto no existe, devolver un mensaje de error
                return response()->json([
                    'code' => 404,
                    'data' => 'Producto no encontrado'
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }
}