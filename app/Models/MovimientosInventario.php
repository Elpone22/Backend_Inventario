<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MovimientosInventario extends Model
{
    use HasFactory;

    protected $fillable = ['id' ,'cantidad', 'fecha', 'tipoMov', 'fk_productos'];

    
}
