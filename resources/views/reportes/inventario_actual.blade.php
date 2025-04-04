<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Inventario Actual</title>
    <style>
        @page { margin: 50px 25px; }
        body { 
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header { 
            position: fixed; 
            top: -30px; 
            left: 0px; 
            right: 0px; 
            height: 50px; 
            text-align: center;
            border-bottom: 1px solid #eee;
        }
        .footer { 
            position: fixed; 
            bottom: -30px; 
            left: 0px; 
            right: 0px; 
            height: 30px; 
            text-align: center;
            border-top: 1px solid #eee;
        }
        .page-number:before { content: "Página " counter(page); }
        .logo { max-height: 40px; }
        .table { 
            width: 100%; 
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th { 
            background-color: #f8f9fa;
            padding: 8px;
            text-align: left;
        }
        .table td { 
            padding: 6px;
            border-top: 1px solid #ddd;
            vertical-align: middle;
        }
        .img-producto { 
            max-width: 50px; 
            max-height: 50px; 
            display: block;
            margin: 0 auto;
        }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .badge {
            display: inline-block;
            padding: 3px 7px;
            font-size: 12px;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <table width="100%">
            <tr>
                <td width="30%">
                    <img src="{{ public_path('images/logo.png') }}" class="logo">
                </td>
                <td width="40%" style="text-align: center;">
                    <h2 style="margin:0;">Reporte de Inventario</h2>
                    <small>{{ now()->setTimezone('America/Mexico_City')->format('d/m/y H:i') }}</small>
                </td>
                <td width="30%" style="text-align: right;"></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <div class="page-number"></div>
    </div>

    <div style="margin-top: 50px;">
        <table class="table">
            <thead>
                <tr>
                    <th width="15%">Imagen</th>
                    <th width="40%">Producto</th>
                    <th width="20%" style="text-align: right;">Cantidad</th>
                    <th width="25%">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inventario as $producto)
                <tr>
                    <td style="text-align: center;">
                        @if($producto->imagen)
                            <img src="{{ $producto->imagen }}" class="img-producto">
                        @else
                            <span style="font-size: 24px;">📷</span>
                        @endif
                    </td>
                    <td>{{ $producto->nombre ?? 'Sin nombre' }}</td>
                    <td style="text-align: right;">
                        <span class="badge" style="background-color: #007bff; color: white;">
                            {{ $producto->cantidad_total ?? 0 }}
                        </span>
                    </td>
                    <td>
                        @if($producto->cantidad_total > 0)
                            <span class="text-success">✓ Disponible</span>
                        @else
                            <span class="text-danger">✗ Agotado</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>