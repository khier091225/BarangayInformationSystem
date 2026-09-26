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
        Schema::table('residents', function (Blueprint $table) {
            $table->string('registration_code_hash', 64)->nullable()->unique();
            $table->timestamp('registration_code_expires_at')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('resident_id')->nullable()->unique()->constrained()->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('resident_id');
        });

        Schema::table('residents', function (Blueprint $table) {
            $table->dropColumn(['registration_code_hash', 'registration_code_expires_at']);
        });
    }
};
