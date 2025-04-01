<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Inventario Actual</title>
</head>
<body>
    <h1>Reporte de Inventario Actual</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventario as $producto)
            <tr>
                <td>{{ $producto->nombre ?? 'Sin producto' }}</td>
                <td>{{ $producto->cantidad   }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
