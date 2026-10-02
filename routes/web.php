<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\BlotterController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HouseholdController;
use App\Http\Controllers\IncidentReportEvidenceController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NewPasswordController;
use App\Http\Controllers\OfficialController;
use App\Http\Controllers\PasswordResetLinkController;
use App\Http\Controllers\ProjectChatbotController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResidentController;
use App\Http\Controllers\ResidentIncidentReportController;
use App\Http\Controllers\ResidentProfileController;
use App\Http\Controllers\ResidentRegistrationCodeController;
use App\Http\Controllers\ResidentServiceRequestController;
use App\Http\Controllers\StaffIncidentReportController;
use App\Http\Controllers\StaffProfileController;
use App\Http\Controllers\StaffServiceRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/chatbot/reply', ProjectChatbotController::class)
    ->middleware('throttle:8,1')->name('chatbot.reply');
Route::redirect('/home', '/');
Route::redirect('/portal', '/');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('throttle:5,1')->name('login.store');
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:3,1')->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->middleware('throttle:5,1')->name('password.update');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])
        ->middleware('throttle:5,1')->name('register.store');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/account', [AccountController::class, 'index'])->name('account');
    Route::post('/account/verify', [AccountController::class, 'verify'])
        ->middleware('throttle:5,1')->name('account.verify');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/incident-evidence/{incidentReport}', IncidentReportEvidenceController::class)
        ->name('incident-reports.evidence');
});

Route::middleware(['auth', 'resident.verified'])->group(function (): void {
    Route::get('/account/profile', [ResidentProfileController::class, 'edit'])->name('account.profile.edit');
    Route::patch('/account/profile/password', [ResidentProfileController::class, 'updatePassword'])
        ->middleware('throttle:5,1')->name('account.profile.password.update');
    Route::get('/account/requests', [ResidentServiceRequestController::class, 'index'])->name('account.requests.index');
    Route::get('/account/requests/certificate', [ResidentServiceRequestController::class, 'createCertificate'])->name('account.requests.certificate.create');
    Route::post('/account/requests/certificate', [ResidentServiceRequestController::class, 'storeCertificate'])
        ->middleware('throttle:5,1')->name('account.requests.certificate.store');
    Route::get('/account/requests/blotter', [ResidentServiceRequestController::class, 'createBlotter'])->name('account.requests.blotter.create');
    Route::post('/account/requests/blotter', [ResidentServiceRequestController::class, 'storeBlotter'])
        ->middleware('throttle:5,1')->name('account.requests.blotter.store');
    Route::get('/account/requests/{serviceRequest}', [ResidentServiceRequestController::class, 'show'])->name('account.requests.show');
    Route::get('/account/incidents', [ResidentIncidentReportController::class, 'index'])->name('account.incidents.index');
    Route::get('/account/incidents/create', [ResidentIncidentReportController::class, 'create'])->name('account.incidents.create');
    Route::post('/account/incidents', [ResidentIncidentReportController::class, 'store'])
        ->middleware('throttle:5,1')->name('account.incidents.store');
    Route::get('/account/incidents/{incidentReport}', [ResidentIncidentReportController::class, 'show'])->name('account.incidents.show');
});

Route::middleware('staff.session')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [StaffProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [StaffProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [StaffProfileController::class, 'updatePassword'])
        ->middleware('throttle:5,1')->name('profile.password.update');
    Route::resource('residents', ResidentController::class);
    Route::post('/residents/{resident}/registration-code', [ResidentRegistrationCodeController::class, 'store'])
        ->name('residents.registration-code.store');
    Route::get('/service-requests', [StaffServiceRequestController::class, 'index'])->name('service-requests.index');
    Route::get('/service-requests/{serviceRequest}', [StaffServiceRequestController::class, 'show'])->name('service-requests.show');
    Route::post('/service-requests/{serviceRequest}/review', [StaffServiceRequestController::class, 'review'])->name('service-requests.review');
    Route::get('/incident-reports', [StaffIncidentReportController::class, 'index'])->name('incident-reports.index');
    Route::get('/incident-reports/{incidentReport}', [StaffIncidentReportController::class, 'show'])->name('incident-reports.show');
    Route::post('/incident-reports/{incidentReport}/status', [StaffIncidentReportController::class, 'update'])
        ->name('incident-reports.update');
    Route::resource('households', HouseholdController::class);
    Route::post('/blotters/{blotter}/status', [BlotterController::class, 'updateStatus'])
        ->name('blotters.status.update');
    Route::resource('blotters', BlotterController::class);
    Route::resource('officials', OfficialController::class)->except(['show']);
    Route::resource('certificates', CertificateController::class)->except(['edit', 'update']);
});
