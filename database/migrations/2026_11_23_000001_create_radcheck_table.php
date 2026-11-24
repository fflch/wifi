<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabela padrão do FreeRADIUS (schema oficial do rlm_sql).
 *
 * O Laravel é o único que escreve aqui (App\Services\RadiusService);
 * o FreeRADIUS apenas lê. Cada MAC liberado vira uma linha:
 *   username = 'aabbccddeeff', attribute = 'Auth-Type', op = ':=', value = 'Accept'
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('radcheck', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 64)->default('');
            $table->string('attribute', 64)->default('');
            $table->char('op', 2)->default('==');
            $table->string('value', 253)->default('');

            $table->index('username');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radcheck');
    }
};
