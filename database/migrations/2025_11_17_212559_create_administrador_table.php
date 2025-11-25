<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('administrador', function (Blueprint $table) {
            $table->increments('idAdmin');
            $table->string('area', 100)->nullable();
            $table->unsignedInteger('idAuth')->nullable();
            $table->boolean('activo')->default(true);
            $table->foreign('idAuth')->references('idAuth')->on('doblefactorauth');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('administrador');
    }
};
