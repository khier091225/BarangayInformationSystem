<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCertificateRequest;
use App\Models\Certificate;
use App\Models\Official;
use App\Models\Resident;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * Display a listing of issued certificates.
     */
    public function index(Request $request)
    {
        /*
        $query = Certificate::with('resident');

        // Search by resident name, certificate type, or purpose
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('certificate_type', 'like', "%{$search}%")
                    ->orWhere('purpose', 'like', "%{$search}%")
                    ->orWhereHas('resident', function ($rq) use ($search) {
                        $rq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('middle_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by certificate type
        if ($request->filled('type')) {
            $query->where('certificate_type', $request->type);
        }

        $certificates = $query->latest('date_issued')->paginate(10)->withQueryString();
        */

        $certificates = Certificate::latest('date_issued')->paginate(10)->appends(['role' => 'admin']);

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

        return redirect()->route('certificates.show', [$certificate, 'role' => 'admin'])
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

        return redirect()->route('certificates.index', ['role' => 'admin'])
            ->with('success', 'Certificate record deleted successfully!');
    }
}
