@extends('layouts.app')

@section('title')
    {{ $certificate->certificate_type }} - {{ $certificate->resident->full_name }}
@endsection

@section('main-style', 'padding: 30px;')

@push('styles')
<style>
    @media print {
        .no-print, .workspace-sidebar, .workspace-topbar, .action-bar {
            display: none !important;
        }
        body, .workspace-page, .workspace-shell, .workspace-main {
            background: white !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .certificate-sheet {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }
    }
    .certificate-sheet {
        background: white;
        width: 210mm;
        min-height: 297mm;
        margin: 0 auto;
        padding: 40px 50px;
        border: 1px solid #dcdfdc;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        box-sizing: border-box;
        font-family: 'Times New Roman', Times, serif;
        color: #111;
        position: relative;
    }
    .cert-header {
        text-align: center;
        border-bottom: 2px solid #224d35;
        padding-bottom: 15px;
        margin-bottom: 30px;
    }
    .cert-header h3 {
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 0;
        font-weight: normal;
        color: #444;
    }
    .cert-header h2 {
        font-size: 18px;
        margin: 4px 0;
        color: #1e3a29;
        font-weight: bold;
    }
    .cert-header h1 {
        font-size: 15px;
        letter-spacing: 2px;
        text-transform: uppercase;
        margin: 4px 0 0 0;
        color: #276747;
    }
    .cert-title-container {
        text-align: center;
        margin: 35px 0 30px 0;
    }
    .cert-title {
        font-size: 24px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 2px;
        text-decoration: underline;
        color: #153823;
    }
    .cert-body {
        font-size: 15px;
        line-height: 1.8;
        text-align: justify;
        margin-bottom: 30px;
    }
    .cert-body p {
        margin-bottom: 20px;
        text-indent: 40px;
    }
    .cert-footer {
        margin-top: 60px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }
    .thumb-box {
        width: 90px;
        height: 90px;
        border: 1px solid #888;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        color: #777;
        text-align: center;
    }
    .signature-block {
        text-align: center;
        min-width: 220px;
    }
    .signature-line {
        border-bottom: 1px solid #222;
        margin-bottom: 6px;
        font-weight: bold;
        font-size: 16px;
        text-transform: uppercase;
    }
</style>
@endpush

@section('topbar')
    <!-- Top Actions Toolbar (Hidden during print) -->
    <div class="action-bar no-print" style="padding: 18px 34px; background: white; border-bottom: 1px solid #e1e7de; display: flex; justify-content: space-between; align-items: center;">
        <a href="{{ route('certificates.index', ['role' => 'admin']) }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #42634e; text-decoration: none; font-weight: 500;">
            <i data-lucide="arrow-left"></i> Back to Certificates
        </a>

        <div style="display: flex; gap: 12px;">
            <button onclick="window.print()" class="button button-primary" style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; padding: 10px 20px;">
                <i data-lucide="printer"></i> Print Certificate (PDF)
            </button>
        </div>
    </div>
@endsection

@section('content')
    <div class="certificate-sheet">
        <!-- Republic Letterhead -->
        <div class="cert-header">
            <h3>Republic of the Philippines</h3>
            <h3>Province of Laguna • Municipality of Calamba</h3>
            <h2>BARANGAY KAY-ANLOG</h2>
            <h1>Office of the Punong Barangay</h1>
        </div>

        <!-- Certificate Title -->
        <div class="cert-title-container">
            <div class="cert-title">{{ $certificate->certificate_type }}</div>
            <div style="font-size: 12px; color: #555; margin-top: 6px;">Control No: CERT-{{ str_pad($certificate->id, 5, '0', STR_PAD_LEFT) }}</div>
        </div>

        <!-- Certification Body -->
        <div class="cert-body">
            <p style="text-indent: 0; font-weight: bold; margin-bottom: 16px;">TO WHOM IT MAY CONCERN:</p>

            <p>
                <strong>THIS IS TO CERTIFY</strong> that <strong>{{ strtoupper($certificate->resident->full_name) }}</strong>, 
                of legal age, Filipino citizen, is a bonafide resident of this Barangay with postal address at 
                <strong>{{ $certificate->resident->address }}</strong>.
            </p>

            @if($certificate->certificate_type == 'Barangay Clearance')
                <p>
                    Records of this office further show that the said person has <strong>NO DEROGATORY RECORD</strong> 
                    nor any pending criminal case filed against him/her in this Barangay as of this date, and is known 
                    to be of good moral character and a law-abiding citizen of the community.
                </p>
            @elseif($certificate->certificate_type == 'Certificate of Indigency')
                <p>
                    Records further show that the subject resident belongs to an <strong>INDIGENT FAMILY</strong> 
                    in this Barangay, with low and irregular household income, qualified to receive social, medical, 
                    legal, or educational assistance from government agencies and private institutions.
                </p>
            @else
                <p>
                    This certifies that the above-named individual has been continuously residing in this Barangay 
                    and is actively recognized in our official registry of residents.
                </p>
            @endif

            <p>
                This certification is issued upon the request of the interested party for the purpose of 
                <strong>{{ strtoupper($certificate->purpose) }}</strong> and for whatever legal intent it may serve.
            </p>

            <p>
                <strong>GIVEN</strong> and signed this <strong>{{ $certificate->date_issued ? $certificate->date_issued->format('jS') : date('jS') }}</strong> 
                day of <strong>{{ $certificate->date_issued ? $certificate->date_issued->format('F, Y') : date('F, Y') }}</strong> 
                at the Office of the Punong Barangay.
            </p>
        </div>

        <!-- Footer & Signatures -->
        <div class="cert-footer">
            <div>
                <div class="thumb-box">
                    Right Thumbmark
                </div>
                <div style="margin-top: 15px; font-size: 12px;">
                    <div><strong>Applicant Signature:</strong></div>
                    <div style="width: 140px; border-bottom: 1px solid #555; margin-top: 30px;"></div>
                </div>
            </div>

            <div class="signature-block">
                <div class="signature-line">
                    {{ $captain ? $captain->name : 'HON. BARANGAY CAPTAIN' }}
                </div>
                <div style="font-size: 13px; font-weight: bold; color: #333;">Punong Barangay</div>
                <div style="font-size: 11px; color: #666; margin-top: 2px;">Official Seal of Barangay</div>
            </div>
        </div>
    </div>
@endsection
