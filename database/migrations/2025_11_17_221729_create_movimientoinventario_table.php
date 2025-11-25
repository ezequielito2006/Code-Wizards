<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up() {
        Schema::create('movimientoinventario', function (Blueprint $table) {
            $table->increments('idMovimiento');
            $table->unsignedInteger('idProducto');
            $table->string('tipo', 20); // entrada, salida, ajuste
            $table->integer('cantidad');
            $table->string('descripcion', 255)->nullable();
            $table->dateTime('fecha')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->unsignedInteger('idUsuario')->nullable();
            $table->boolean('activo')->default(true);
            $table->foreign('idProducto')->references('idProducto')->on('producto');
            $table->foreign('idUsuario')->references('idUsuario')->on('usuario');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('movimientoinventario');
    }
};
