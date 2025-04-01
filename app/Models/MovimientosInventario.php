<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Producto;

class MovimientosInventario extends Model
{
    use HasFactory;

    protected $table = 'movimientos_inventarios';

    protected $fillable = [
        'cantidad',
        'fecha',
        'tipoMov',
        'fk_productos',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'fk_productos');
    }
}
