<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Maquinaria extends Model
{
    use HasFactory;
    protected $table = 'maquinaria';
    protected $fillable = ['nombre', 'modelo','tipo'];
}
