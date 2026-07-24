<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("visitors", function (Blueprint $table) {
            $table->char("id", 26)->primary();
            $table->string("name");
            $table->string("document", 20);
            $table->string("email");
            $table->string("phone", 20)->nullable();
            $table->string("client_mac", 17);
            $table->timestamps();
            $table->softDeletes();

            $table->unique("email");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("visitors");
    }
};
