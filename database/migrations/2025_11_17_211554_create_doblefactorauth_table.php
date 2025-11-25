<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('doblefactorauth', function (Blueprint $table) {
            $table->increments('idAuth');
            $table->string('codigo', 6); // ✅ código como string de 6 dígitos
            $table->dateTime('fechaEnvio'); // ✅ datetime para guardar fecha y hora
            $table->boolean('estado')->default(true);
            $table->unsignedInteger('idUsuario')->nullable();
            $table->boolean('activo')->default(true);

            $table->foreign('idUsuario')
                  ->references('idUsuario')
                  ->on('usuario')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('doblefactorauth');
    }
};
