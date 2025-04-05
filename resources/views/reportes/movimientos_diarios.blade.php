<!DOCTYPE html>
<html>
<head>
    <title>Movimientos Diarios</title>
    <style>
        /* Estilos similares a los que usamos en inventario_actual */
        .table { width: 100%; border-collapse: collapse; }
        .table th { background: #f3f3f3; padding: 8px; }
        .table td { padding: 2px; border-top: 1px solid #ddd; }
        .text-success { color: green; }
        .text-danger { color: red; }
    </style>
</head>
<body>
    <h2>Movimientos Diarios - {{ $fecha }}</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Tipo</th>
                <th>Cantidad</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movimientos as $mov)
            <tr>
                <td>{{ $mov->producto->nombre ?? 'N/A' }}</td>
                <td class="{{ $mov->cantidad > 0 ? 'text-success' : 'text-danger' }}">
                    {{ $mov->tipoMov}}
                </td>
                <td>{{ abs($mov->cantidad) }}</td>
                <td>{{ $mov->usuario->name ?? 'Sistema' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>