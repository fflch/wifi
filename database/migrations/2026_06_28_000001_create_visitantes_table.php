<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitantes', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('nome');
            $table->string('email');
            $table->string('documento', 20);
            $table->string('telefone', 20)->nullable();
            $table->string('mac', 17)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitantes');
    }
};
