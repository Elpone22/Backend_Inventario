<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Movimientos - {{ $producto->nombre ?? 'Producto' }}</title>
    <style>
        @page { margin: 50px 25px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; line-height: 1.5; }
        .header { position: fixed; top: -30px; left: 0; right: 0; height: 50px; text-align: center; }
        .footer { position: fixed; bottom: -30px; left: 0; right: 0; height: 30px; text-align: center; }
        .table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .table th { background: #f3f3f3; padding: 8px; text-align: left; border: 1px solid #ddd; }
        .table td { padding: 6px; border: 1px solid #ddd; }
        .img-producto { max-width: 60px; max-height: 60px; border-radius: 4px; }
        .producto-info { margin-bottom: 20px; padding: 15px; background: #f9f9f9; border-radius: 4px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .entrada { color: #28a745; font-weight: bold; }
        .salida { color: #dc3545; font-weight: bold; }
        .resumen { background-color: #f8f9fa; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Reporte de Movimientos - {{ $nombreProducto }}</h2>
        <div style="font-size: 10px; color: #666;">Generado el {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <div class="footer">
        <div class="page-number">Página <span class="page"></span> de <span class="topage"></span></div>
    </div>

    <div style="margin-top: 60px;">
        @if($producto)
        <div class="producto-info">
            <div style="display: flex; align-items: center; gap: 20px;">
                @if($producto->imagen_url)
                <div>
                    <img src="{{ $producto->imagen_url }}" class="img-producto">
                </div>
                @endif
                <div>
                    <h3 style="margin: 0 0 10px 0;">{{ $producto->nombre }}</h3>
                    <p style="margin: 5px 0;"><strong>Referencia:</strong> {{ $producto->referencia ?? 'N/A' }}</p>
                    <p style="margin: 5px 0;"><strong>Categoría:</strong> {{ $producto->categoria->nombre ?? 'N/A' }}</p>
                    <p style="margin: 5px 0;"><strong>Marca:</strong> {{ $producto->marca->nombre ?? 'N/A' }}</p>
                    <p style="margin: 5px 0;"><strong>Total Movimientos:</strong> {{ $movimientos->count() }}</p>
                </div>
            </div>
        </div>
        @endif

        <div class="chart-container">
            <canvas id="movimientosChart"></canvas>
        </div>

        <h3 class="text-center">Detalle de Movimientos</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th class="text-right">Cantidad</th>
                    <th>Descripción</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $saldo = 0;
                @endphp
                @foreach($movimientos as $mov)
                @php
                    $esEntrada = $mov->tipoMov === 'entrada';
                    $saldo += $esEntrada ? $mov->cantidad : -$mov->cantidad;
                @endphp
                <tr>
                    <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <span style="display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 11px; font-weight: bold; color: white; background-color: {{ $esEntrada ? '#28a745' : '#dc3545' }};">
                            {{ $esEntrada ? 'ENTRADA' : 'SALIDA' }}
                        </span>
                    </td>
                    <td class="text-right" style="color: {{ $esEntrada ? '#28a745' : '#dc3545' }}; font-weight: bold;">
                        {{ $esEntrada ? '+' : '-' }}{{ number_format($mov->cantidad, 0, ',', '.') }}
                    </td>
                    <td>{{ $mov->descripcion ?? 'Sin descripción' }}</td>
                    <td>{{ $mov->usuario->name ?? 'Sistema' }}</td>
                </tr>
                @endforeach
                <tr style="background-color: #f8f9fa; font-weight: bold;">
                    <td colspan="2" class="text-right">Saldo Final:</td>
                    <td class="text-right">{{ number_format($saldo, 0, ',', '.') }}</td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        // Datos para la gráfica
        const datosGrafica = {!! $datosGraficaJson !!};
        
        // Configuración de la gráfica
        const ctx = document.getElementById('movimientosChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: datosGrafica.fechas,
                datasets: [
                    {
                        label: 'Entradas',
                        data: datosGrafica.entradas,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Salidas',
                        data: datosGrafica.salidas,
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Saldo',
                        data: datosGrafica.saldos,
                        borderColor: '#007bff',
                        backgroundColor: 'transparent',
                        borderWidth: 3,
                        borderDash: [5, 5],
                        pointRadius: 0,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Historial de Movimientos',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        padding: {
                            bottom: 20
                        }
                    },
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 12
                            },
                            padding: 20
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            size: 12,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 12
                        },
                        padding: 10
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45,
                            font: {
                                size: 10
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: {
                                size: 10
                            },
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    }
                },
                elements: {
                    point: {
                        radius: 0
                    }
                },
                animation: {
                    duration: 0
                }
            }
        });
    </script>
</body>
</html>