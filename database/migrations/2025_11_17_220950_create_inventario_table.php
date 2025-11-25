<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('inventario', function (Blueprint $table) {
            $table->increments('idInventario');
            $table->date('fechaActualizacion');
            $table->integer('stockActual')->default(0);
            $table->unsignedInteger('idProducto')->nullable();
            $table->boolean('activo')->default(true);
            $table->foreign('idProducto')->references('idProducto')->on('producto');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('inventario');
    }
};
