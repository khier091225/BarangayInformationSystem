<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->decimal('fee_amount', 10, 2)->nullable()->after('purpose');
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->decimal('fee', 10, 2)->default(0)->after('purpose');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn('fee');
        });

        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn('fee_amount');
        });
    }
};
