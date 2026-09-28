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
        Schema::create('incident_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->constrained()->restrictOnDelete();
            $table->string('reference_number')->nullable()->unique();
            $table->string('category');
            $table->text('description');
            $table->string('location');
            $table->dateTime('occurred_at');
            $table->boolean('keep_identity_confidential')->default(false);
            $table->string('evidence_path')->nullable();
            $table->string('evidence_original_name')->nullable();
            $table->string('evidence_mime')->nullable();
            $table->string('status')->default('Submitted');
            $table->string('suggested_team');
            $table->string('assigned_team')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('responding_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->string('alert_sms_status')->nullable();
            $table->string('alert_email_status')->nullable();
            $table->timestamps();
            $table->index(['resident_id', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index(['suggested_team', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_reports');
    }
};
