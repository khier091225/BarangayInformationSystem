<?php

namespace App\Http\Controllers;

use App\Models\Blotter;
use App\Models\Certificate;
use App\Models\Household;
use App\Models\IncidentReport;
use App\Models\Official;
use App\Models\Resident;
use App\Models\ServiceRequest;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $residentCount = Resident::count();
        $householdCount = Household::count();
        $certificateCount = Certificate::count();
        $pendingBlotterCount = Blotter::where('status', 'Pending')->count();
        $pendingIncidentReportCount = IncidentReport::where('status', IncidentReport::STATUS_SUBMITTED)->count();
        $totalBlotterCount = Blotter::count();
        $officialCount = Official::count();
        $requestTotals = ServiceRequest::query()
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $requestCounts = [
            'Pending' => (int) ($requestTotals[ServiceRequest::STATUS_PENDING] ?? 0),
            'Awaiting Payment' => (int) ($requestTotals[ServiceRequest::STATUS_AWAITING_PAYMENT] ?? 0),
            'Completed' => (int) ($requestTotals[ServiceRequest::STATUS_COMPLETED] ?? 0),
            'Declined' => (int) ($requestTotals[ServiceRequest::STATUS_DECLINED] ?? 0),
        ];
        $totalServiceRequestCount = array_sum($requestCounts);
        $pendingServiceRequestCount = $requestCounts[ServiceRequest::STATUS_PENDING];
        $pendingRequests = ServiceRequest::query()->with('resident')
            ->where('status', ServiceRequest::STATUS_PENDING)
            ->oldest()->orderBy('id')->limit(4)->get();
        $certificateActivity = $this->certificateActivity();

        $recentCertificates = Certificate::with('resident')->whereDate('date_issued', '<=', today('Asia/Manila')->toDateString())
            ->latest('date_issued')->latest('id')->limit(3)->get();
        $recentBlotters = Blotter::latest()->limit(3)->get();
        $recentResidents = Resident::with('household')->latest()->limit(3)->get();

        return view('dashboard', compact(
            'residentCount',
            'householdCount',
            'certificateCount',
            'pendingBlotterCount',
            'pendingIncidentReportCount',
            'totalBlotterCount',
            'officialCount',
            'pendingServiceRequestCount',
            'totalServiceRequestCount',
            'requestCounts',
            'pendingRequests',
            'certificateActivity',
            'recentCertificates',
            'recentBlotters',
            'recentResidents'
        ));
    }

    /**
     * @return array{months: array<int, array{label: string, fullLabel: string, count: int}>, maximum: int, total: int, currentMonth: int}
     */
    private function certificateActivity(): array
    {
        $firstMonth = today('Asia/Manila')->toImmutable()->startOfMonth()->subMonths(5);
        $query = Certificate::query()
            ->where('date_issued', '>=', $firstMonth->toDateString())
            ->where('date_issued', '<', today('Asia/Manila')->addDay()->toDateString());
        $months = [];

        for ($index = 0; $index < 6; $index++) {
            $month = $firstMonth->addMonths($index);
            $query->selectRaw(
                "SUM(CASE WHEN date_issued >= ? AND date_issued < ? THEN 1 ELSE 0 END) AS month_{$index}",
                [$month->toDateString(), $month->addMonth()->toDateString()],
            );
            $months[] = ['label' => $month->format('M'), 'fullLabel' => $month->format('F Y'), 'count' => 0];
        }

        $totals = $query->toBase()->first();

        foreach ($months as $index => $month) {
            $months[$index]['count'] = (int) ($totals->{"month_{$index}"} ?? 0);
        }

        $counts = array_column($months, 'count');

        return [
            'months' => $months,
            'maximum' => max(4, (int) (ceil(max($counts) / 4) * 4)),
            'total' => array_sum($counts),
            'currentMonth' => $counts[5],
        ];
    }
}
