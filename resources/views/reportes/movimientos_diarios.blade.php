<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Movimientos Diarios</title>
</head>
<body>
    <h1>Reporte de Movimientos Diarios</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Tipo</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movimientos as $movimiento)
            <tr>
                <td>{{ $movimiento->id }}</td>
                <td>{{ $movimiento->producto ? $movimiento->producto->nombre : 'N/A' }}</td>
                <td>{{ $movimiento->cantidad }}</td>
                <td>{{ $movimiento->tipoMov }}</td>
                <td>{{ $movimiento->fecha }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
