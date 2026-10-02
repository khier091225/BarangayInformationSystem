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
        Schema::create('incident_alert_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('team')->unique();
            $table->string('contact_name')->nullable();
            $table->string('phone', 32)->nullable();
            $table->string('email')->nullable();
            $table->boolean('sms_enabled')->default(true);
            $table->boolean('email_enabled')->default(true);
            $table->boolean('is_active')->default(true);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('incident_alert_contact_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_alert_contact_id')->constrained()->cascadeOnDelete();
            $table->string('team')->index();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('before_values')->nullable();
            $table->json('after_values');
            $table->json('changed_fields');
            $table->timestamp('created_at');
            $table->index(['incident_alert_contact_id', 'created_at'], 'incident_alert_contact_history_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_alert_contact_changes');
        Schema::dropIfExists('incident_alert_contacts');
    }
};
