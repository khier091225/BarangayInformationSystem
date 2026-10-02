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
        Schema::table('payments', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->change();
            $table->string('receipt_number')->nullable()->after('paid_at');
            $table->text('payment_note')->nullable()->after('receipt_number');
            $table->foreignId('recorded_by')->nullable()->after('payment_note')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('recorded_by');
            $table->dropColumn(['receipt_number', 'payment_note']);
            $table->timestamp('expires_at')->nullable(false)->change();
        });
    }
};
