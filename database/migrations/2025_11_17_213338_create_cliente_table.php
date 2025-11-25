<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('cliente', function (Blueprint $table) {
            $table->increments('idCliente');
            $table->string('direccion', 255)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->unsignedInteger('idAdmin')->nullable();
            $table->unsignedInteger('idAuth')->nullable();
            $table->boolean('activo')->default(true);
            $table->foreign('idAdmin')->references('idAdmin')->on('administrador');
            $table->foreign('idAuth')->references('idAuth')->on('doblefactorauth');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('cliente');
    }
};
