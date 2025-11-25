<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('detallepedido', function (Blueprint $table) {
            $table->increments('idDetalle');
            $table->integer('cantidad');
            $table->decimal('subTotal', 10, 2);
            $table->unsignedInteger('idPedido')->nullable();
            $table->unsignedInteger('idProducto')->nullable();
            $table->boolean('activo')->default(true);
            $table->foreign('idPedido')->references('idPedido')->on('pedido');
            $table->foreign('idProducto')->references('idProducto')->on('producto');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('detallepedido');
    }
};
