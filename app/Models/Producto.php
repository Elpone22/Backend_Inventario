<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'cantidad',
    ];

    public function movimientos()
    {
        return $this->hasMany(MovimientosInventario::class, 'fk_productos');
    }

        // En app/Models/Producto.php
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'fk_categoria');
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'fk_marca');
    }
}
