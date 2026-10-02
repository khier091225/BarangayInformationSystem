<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $rates = [
            'Barangay Clearance' => '50.00',
            'Certificate of Residency' => '50.00',
            'Certificate of Indigency' => '0.00',
            'Business Clearance' => '100.00',
        ];

        foreach ($rates as $certificateType => $fee) {
            $requestIds = DB::table('service_requests')
                ->where('type', 'certificate')
                ->where('certificate_type', $certificateType)
                ->whereIn('status', ['Pending', 'Awaiting Payment'])
                ->pluck('id');

            if ($requestIds->isEmpty()) {
                continue;
            }

            DB::table('service_requests')->whereIn('id', $requestIds)->update(['fee_amount' => $fee]);
            DB::table('payments')
                ->whereIn('service_request_id', $requestIds)
                ->where('status', 'pending')
                ->update(['amount' => $fee]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /** Data fees cannot be restored because the previous values varied per request. */
    }
};
