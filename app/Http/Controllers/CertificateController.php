<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCertificateRequest;
use App\Models\Certificate;
use App\Models\Official;
use App\Models\Resident;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificateController extends Controller
{
    /**
     * Display a listing of issued certificates.
     */
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
        ]);

        $query = Certificate::with('resident');

        if ($request->filled('search')) {
            $terms = preg_split('/\s+/', trim($filters['search']));

            foreach ($terms as $term) {
                $query->where(function (Builder $certificateQuery) use ($term): void {
                    $certificateQuery->where('certificate_type', 'like', "%{$term}%")
                        ->orWhere('purpose', 'like', "%{$term}%")
                        ->orWhereHas('resident', function (Builder $residentQuery) use ($term): void {
                            $residentQuery->where('first_name', 'like', "%{$term}%")
                                ->orWhere('middle_name', 'like', "%{$term}%")
                                ->orWhere('last_name', 'like', "%{$term}%");
                        });
                });
            }
        }

        if (! empty($filters['type'])) {
            $query->where('certificate_type', $filters['type']);
        }

        $certificates = $query->latest('date_issued')->paginate(10)->withQueryString();

        return view('certificates.index', compact('certificates'));
    }

    /**
     * Show the form for issuing a new certificate.
     */
    public function create(Request $request)
    {
        // Pwedeng mag-pass ng resident_id galing sa profile view
        $selectedResidentId = $request->query('resident_id');

        $residents = Resident::orderBy('last_name')->get();

        return view('certificates.create', compact('residents', 'selectedResidentId'));
    }

    /**
     * Store a newly issued certificate in storage.
     */
    public function store(StoreCertificateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $certificate = Certificate::create($validated);

        return redirect()->route('certificates.show', [$certificate])
            ->with('success', 'Certificate issued successfully! You may now print it.');
    }

    /**
     * Display the printable certificate document.
     */
    public function show(Certificate $certificate)
    {
        $certificate->load('resident.household');

        // Kukunin ang Punong Barangay / Captain para sa signature block
        $captain = Official::where('position', 'like', '%Captain%')
            ->orWhere('position', 'like', '%Punong Barangay%')
            ->first();

        return view('certificates.show', compact('certificate', 'captain'));
    }

    /**
     * Remove or void the certificate record.
     */
    public function destroy(Certificate $certificate)
    {
        $certificate->delete();

        return redirect()->route('certificates.index')
            ->with('success', 'Certificate record deleted successfully!');
    }
}
