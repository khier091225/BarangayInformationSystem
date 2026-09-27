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
        Schema::table('residents', function (Blueprint $table): void {
            $table->timestamp('registration_code_issued_at')->nullable();
            $table->string('registration_code_sms_status', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table): void {
            $table->dropColumn(['registration_code_issued_at', 'registration_code_sms_status']);
        });
    }
};
