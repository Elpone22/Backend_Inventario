<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->float('precio');
            $table->integer('cantidad');
            $table->string('descripcion');
            $table->unsignedBigInteger('fk_marca');
            $table->unsignedBigInteger('fk_categoria');
            $table->string('imagen')->nullable(); // Campo para la imagen
            $table->foreign('fk_marca')->references('id')->on('marcas')->onDelete('cascade');
            $table->foreign('fk_categoria')->references('id')->on('categorias')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
