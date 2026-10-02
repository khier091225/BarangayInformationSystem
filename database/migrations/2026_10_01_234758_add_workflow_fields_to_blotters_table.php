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
        Schema::table('blotters', function (Blueprint $table) {
            $table->foreignId('assigned_to')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->timestamp('accepted_at')->nullable()->after('assigned_to');
            $table->timestamp('hearing_at')->nullable()->after('accepted_at');
            $table->timestamp('mediation_started_at')->nullable()->after('hearing_at');
            $table->timestamp('closed_at')->nullable()->after('mediation_started_at');
            $table->index(['status', 'assigned_to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blotters', function (Blueprint $table) {
            $table->dropIndex(['status', 'assigned_to']);
            $table->dropConstrainedForeignId('assigned_to');
            $table->dropColumn(['accepted_at', 'hearing_at', 'mediation_started_at', 'closed_at']);
        });
    }
};
