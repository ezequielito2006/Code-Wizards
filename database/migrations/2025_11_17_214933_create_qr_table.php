<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('qr', function (Blueprint $table) {
            $table->increments('idQR');
            $table->string('codigoQR', 255);
            $table->date('fechaGeneracion');
            $table->string('enlace', 255)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('qr');
    }
};
