<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wifi_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('wifi_requests', 'rejected_by')) {
                $table->foreignId('rejected_by')->nullable()->constrained('users')->after('approved_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('wifi_requests', function (Blueprint $table) {
            if (Schema::hasColumn('wifi_requests', 'rejected_by')) {
                $table->dropForeign(['rejected_by']);
                $table->dropColumn('rejected_by');
            }
        });
    }
};
