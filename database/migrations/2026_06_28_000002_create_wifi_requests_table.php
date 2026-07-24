<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("wifi_requests", function (Blueprint $table) {
            $table->char("id", 26)->primary();
            $table->char("visitor_id", 26);
            $table->text("reason");
            $table->string("status", 20)->default("pending");
            $table->foreignId("approved_by")->nullable()->constrained("users");
            $table->timestamp("expires_at")->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign("visitor_id")->references("id")->on("visitors");
            $table->index("status");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("wifi_requests");
    }
};
