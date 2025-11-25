<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('pedido', function (Blueprint $table) {
            $table->increments('idPedido');
            $table->date('fecha');
            $table->string('estado', 50)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->unsignedInteger('idCliente')->nullable();
            $table->unsignedInteger('idQR')->nullable();
            $table->boolean('activo')->default(true);
            $table->foreign('idCliente')->references('idCliente')->on('cliente');
            $table->foreign('idQR')->references('idQR')->on('qr');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('pedido');
    }
};
