<!DOCTYPE html>
<html>
<head>
    <title>Movimientos por Producto</title>
    <style>
        @page { margin: 50px 25px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; }
        .header { position: fixed; top: -30px; left: 0; right: 0; height: 50px; }
        .footer { position: fixed; bottom: -30px; left: 0; right: 0; height: 30px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th { background: #f3f3f3; padding: 8px; text-align: left; }
        .table td { padding: 6px; border-top: 1px solid #ddd; }
        .img-producto { max-width: 60px; max-height: 60px; }
        .producto-info { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Movimientos del Producto</h2>
    </div>

    <div class="footer">
        <div class="page-number"></div>
    </div>

    <div style="margin-top: 50px;">
        @if($producto)
        <div class="producto-info">
            @if($producto->imagen_url)
            <img src="{{ $producto->imagen_url }}" class="img-producto">
            @endif
            <h3>{{ $producto->nombre }}</h3>
            <p>Categoría: {{ $producto->categoria->nombre ?? 'N/A' }}</p>
            <p>Marca: {{ $producto->marca->nombre ?? 'N/A' }}</p>
        </div>
        @endif

        <table class="table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
                @foreach($movimientos as $mov)
                <tr>
                    <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $mov->cantidad > 0 ? 'Entrada' : 'Salida' }}</td>
                    <td>{{ abs($mov->cantidad) }}</td>
                    <td>{{ $mov->usuario->name ?? 'Sistema' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>