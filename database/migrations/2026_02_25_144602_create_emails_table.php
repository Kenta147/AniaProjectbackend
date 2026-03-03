<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // Nombre/etiqueta de la plantilla
            $table->string('subject');                 // Asunto del email
            $table->longText('body');                  // Cuerpo del email (HTML)
            $table->string('image_url')->nullable();   // Imagen de cabecera en storage
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};
