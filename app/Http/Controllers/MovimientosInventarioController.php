<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MovimientosInventario;
use App\Models\Producto;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;

class MovimientosInventarioController extends Controller
{
    /**
     * Muestra una lista de movimientos de inventario.
     */
    public function index()
    {
        try {
            // Query para consultar movimientos de inventario
            $movimientos = MovimientosInventario::select(
                'movimientos_inventarios.id',
                'movimientos_inventarios.cantidad',
                'movimientos_inventarios.fecha',
                'movimientos_inventarios.tipoMov',
                'productos.nombre as producto' // Nombre del producto
            )
            ->join('productos', 'movimientos_inventarios.fk_productos', '=', 'productos.id') // Relación con productos
            ->get();

            if ($movimientos->count() > 0) {
                // Si hay movimientos, se retorna el listado en un JSON
                return response()->json([
                    'code' => 200,
                    'data' => $movimientos
                ], 200);
            } else {
                // Si no hay movimientos, se retorna un mensaje
                return response()->json([
                    'code' => 404,
                    'data' => 'No hay movimientos realizados'
                ], 404);
            }
        } catch (\Throwable $th) {
            // Manejo de errores
            return response()->json($th->getMessage(), 500);
        }
    }
   

    /**
     * Almacena un nuevo movimiento de inventario en la base de datos.
     */
    public function store(Request $request)
    {
        // Reglas de validación
        $rules = [
            'cantidad' => 'required|numeric',
            'fecha' => 'required|date',
            'tipoMov' => 'required|in:Entrada,Salida',
            'fk_productos' => 'required|exists:productos,id',
        ];

        // Mensajes de error personalizados (opcional)
        $messages = [
            'cantidad.required' => 'La cantidad es obligatoria.',
            'fecha.required' => 'La fecha es obligatoria.',
            'tipoMov.required' => 'El tipo de movimiento es obligatorio.',
            'fk_productos.required' => 'El producto es obligatorio.',
        ];

        // Validar los datos
        $validator = Validator::make($request->all(), $rules, $messages);

        // Si la validación falla
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors()
            ], 400);
        }

        // Obtener el producto asociado
        $producto = Producto::find($request->fk_productos);

        if (!$producto) {
            return response()->json([
                'code' => 404,
                'data' => 'Producto no encontrado'
            ], 404);
        }

        // Validar que no se reste más de la cantidad disponible
        if ($request->tipoMov === 'Salida' && $request->cantidad > $producto->cantidad) {
            return response()->json([
                'code' => 400,
                'data' => 'No hay suficiente cantidad.'
            ], 400);
        }

        // Crear el movimiento
        $movimiento = MovimientosInventario::create($request->all());

        // Actualizar la cantidad del producto según el tipo de movimiento
        if ($request->tipoMov === 'Entrada') {
            $producto->cantidad += $request->cantidad; // Sumar la cantidad
        } elseif ($request->tipoMov === 'Salida') {
            $producto->cantidad -= $request->cantidad; // Restar la cantidad
        }

        // Guardar los cambios en el producto
        $producto->save();

        return response()->json([
            'code' => 200,
            'data' => 'Movimiento realizado'
        ], 200);
    }
    /**
     * Muestra un movimiento de inventario específico.
     */
    public function show(string $id)
    {
        try {
            // Query para consultar un movimiento específico
            $movimiento = MovimientosInventario::select(
                'movimientos_inventarios.id',
                'movimientos_inventarios.cantidad',
                'movimientos_inventarios.fecha',
                'movimientos_inventarios.tipoMov',
                'productos.nombre as producto' // Nombre del producto
            )
            ->join('productos', 'movimientos_inventarios.fk_productos', '=', 'productos.id') // Relación con productos
            ->where('movimientos_inventarios.id', $id) // Filtrar por ID
            ->first(); // Obtener el primer resultado

            if ($movimiento) {
                // Si el movimiento existe, se retorna en un JSON
                return response()->json([
                    'code' => 200,
                    'data' => $movimiento
                ], 200);
            } else {
                // Si el movimiento no existe, se retorna un mensaje
                return response()->json([
                    'code' => 404,
                    'data' => 'Movimiento no encontrado'
                ], 404);
            }
        } catch (\Throwable $th) {
            // Manejo de errores
            return response()->json($th->getMessage(), 500);
        }
    }

    /**
     * Actualiza un movimiento de inventario en la base de datos.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Se valida que todos los campos sean requeridos
            $validacion = Validator::make($request->all(), [
                'cantidad' => 'required|numeric',
                'fecha' => 'required|date',
                'tipoMov' => 'required|in:Entrada,Salida',
                'fk_productos' => 'required|exists:productos,id', // Validar que el producto exista
            ]);

            if ($validacion->fails()) {
                // Si no se cumple la validación se devuelve el mensaje de error
                return response()->json([
                    'code' => 400,
                    'data' => $validacion->messages()
                ], 400);
            } else {
                // Si se cumple la validación se busca el movimiento
                $movimiento = MovimientosInventario::find($id);
                if ($movimiento) {
                    // Obtener el producto asociado
                    $producto = Producto::find($request->fk_productos);

                    if (!$producto) {
                        return response()->json([
                            'code' => 404,
                            'data' => 'Producto no encontrado'
                        ], 404);
                    }

                    // Validar que no se reste más de la cantidad disponible
                    if ($request->tipoMov === 'Salida' && $request->cantidad > $producto->cantidad) {
                        return response()->json([
                            'code' => 400,
                            'data' => 'No hay suficiente cantidad.'
                        ], 400);
                    }

                    // Revertir la cantidad anterior del movimiento
                    if ($movimiento->tipoMov === 'Entrada') {
                        $producto->cantidad -= $movimiento->cantidad; // Restar la cantidad anterior
                    } elseif ($movimiento->tipoMov === 'Salida') {
                        $producto->cantidad += $movimiento->cantidad; // Sumar la cantidad anterior
                    }

                    // Actualizar el movimiento
                    $movimiento->update($request->all());

                    // Aplicar la nueva cantidad del movimiento
                    if ($request->tipoMov === 'Entrada') {
                        $producto->cantidad += $request->cantidad; // Sumar la nueva cantidad
                    } elseif ($request->tipoMov === 'Salida') {
                        $producto->cantidad -= $request->cantidad; // Restar la nueva cantidad
                    }

                    // Guardar los cambios en el producto
                    $producto->save();

                    return response()->json([
                        'code' => 200,
                        'data' => 'Movimiento actualizado'
                    ], 200);
                } else {
                    // Si el movimiento no existe se devuelve un mensaje
                    return response()->json([
                        'code' => 404,
                        'data' => 'Movimiento no encontrado'
                    ], 404);
                }
            }
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }


    /**
     * Elimina un movimiento de inventario de la base de datos.
     */
    public function destroy(string $id)
    {
        try {
            // Se busca el movimiento
            $movimiento = MovimientosInventario::find($id);
            if ($movimiento) {
                // Obtener el producto asociado
                $producto = Producto::find($movimiento->fk_productos);

                if (!$producto) {
                    return response()->json([
                        'code' => 404,
                        'data' => 'Producto no encontrado'
                    ], 404);
                }

                // Revertir la cantidad del movimiento
                if ($movimiento->tipoMov === 'Entrada') {
                    $producto->cantidad -= $movimiento->cantidad; // Restar la cantidad
                } elseif ($movimiento->tipoMov === 'Salida') {
                    $producto->cantidad += $movimiento->cantidad; // Sumar la cantidad
                }

                // Guardar los cambios en el producto
                $producto->save();

                // Eliminar el movimiento
                $movimiento->delete();

                return response()->json([
                    'code' => 200,
                    'data' => 'Movimiento eliminado'
                ], 200);
            } else {
                // Si el movimiento no existe se devuelve un mensaje
                return response()->json([
                    'code' => 404,
                    'data' => 'Movimiento no encontrado'
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }

    public function reporteMovimientosDiarios(Request $request)
{
    $fecha = $request->input('fecha');
    $movimientos = MovimientosInventario::with('producto')->whereDate('fecha', $fecha)->get();
    $pdf = Pdf::loadView('reportes.movimientos_diarios', compact('movimientos', 'fecha'));
    return $pdf->download('reporte_movimientos_diarios.pdf');
}

public function reporteMovimientosPorProducto(Request $request)
{
    $productoId = $request->input('producto_id');
    \Log::info("Generando reporte para producto ID: $productoId");

    $movimientos = MovimientosInventario::with(['producto' => function($query) {
        $query->when(method_exists(Producto::class, 'categoria'), function($q) {
            $q->with('categoria');
        })
        ->when(method_exists(Producto::class, 'marca'), function($q) {
            $q->with('marca');
        });
    }])
    ->where('fk_productos', $productoId)
    ->get()
    ->map(function($movimiento) use ($productoId) {
        if ($movimiento->producto && $movimiento->producto->imagen) {
            $path = public_path('storage/' . $movimiento->producto->imagen);
            \Log::info("Buscando imagen en: $path");
            
            if (file_exists($path)) {
                $movimiento->producto->imagen_url = 'data:image/'.pathinfo($path, PATHINFO_EXTENSION).';base64,'.base64_encode(file_get_contents($path));
                \Log::info("Imagen encontrada y convertida a base64");
            } else {
                \Log::error("Imagen NO encontrada en: $path");
                $movimiento->producto->imagen_url = null;
            }
        }
        return $movimiento;
    });

    \Log::info("Datos para la vista:", [
        'producto' => $movimientos->first()->producto ?? null,
        'total_movimientos' => $movimientos->count()
    ]);

    $pdf = Pdf::loadView('reportes.movimientos_por_producto', [
        'movimientos' => $movimientos,
        'producto' => $movimientos->first()->producto ?? null
    ]);
    
    return $pdf->stream('reporte_movimientos_por_producto.pdf');
}

public function reporteInventarioActual()
{
    $inventario = Producto::withSum('movimientos as cantidad_total', 'cantidad')
        ->get()
        ->map(function ($producto) {
            if ($producto->imagen) {
                $path = public_path('storage/' . $producto->imagen);
                if (file_exists($path)) {
                    $producto->imagen = 'data:image/'.pathinfo($path, PATHINFO_EXTENSION).';base64,'.base64_encode(file_get_contents($path));
                } else {
                    $producto->imagen = null;
                }
            }
            return $producto;
        });

    $pdf = Pdf::loadView('reportes.inventario_actual', compact('inventario'));
    return $pdf->download('reporte_inventario_actual.pdf');
}


}