<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\TriageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
use App\Http\Controllers\PublicController;

use App\Http\Controllers\AuthController;

Route::get('/', [PublicController::class, 'index'])->name('welcome');
Route::get('/about', [PublicController::class, 'about'])->name('about');
Route::get('/units', [PublicController::class, 'units'])->name('units.index');
Route::get('/units/{slug}', [PublicController::class, 'showUnit'])->name('units.show');
Route::get('/check-home-updates', [PublicController::class, 'checkHomeUpdates'])->name('welcome.check-updates');
Route::get('/announcements/{announcement}', [PublicController::class, 'showAnnouncement'])->name('announcements.show');
Route::get('/announcements/{announcement}/check-update', [PublicController::class, 'checkAnnouncementUpdate'])->name('announcements.check-update');
Route::get('/announcements/{announcement}/fetch-content', [PublicController::class, 'getAnnouncementContent'])->name('announcements.fetch-content');
Route::get('/services/{service}', [PublicController::class, 'showService'])->name('services.show');
Route::get('/appointment', [PublicController::class, 'createAppointment'])->name('appointment.create');
Route::get('/appointment/check-availability', [PublicController::class, 'checkAvailability'])->name('appointment.check-availability');
Route::get('/appointment/time-slots', [PublicController::class, 'getTimeSlots'])->name('appointment.time-slots');
Route::post('/appointment', [PublicController::class, 'storeAppointment'])->middleware('throttle:booking')->name('appointment.store');
Route::post('/appointment/check-duplicate', [PublicController::class, 'checkDuplicate'])->name('appointment.check-duplicate');
Route::post('/appointment/verify-follow-up', [PublicController::class, 'verifyFollowUp'])->name('appointment.verify-follow-up');
Route::post('/appointment/send-otp', [PublicController::class, 'sendOtp'])->name('appointment.send-otp');
Route::post('/appointment/verify-otp', [PublicController::class, 'verifyOtp'])->name('appointment.verify-otp');
Route::get('/appointment/manage', [PublicController::class, 'manageAppointment'])->name('appointment.manage');
Route::post('/appointment/manage', [PublicController::class, 'loginAppointment'])->name('appointment.login');
Route::get('/appointment/dashboard', [PublicController::class, 'dashboardAppointment'])->name('appointment.dashboard');
Route::post('/appointment/cancel', [PublicController::class, 'cancelAppointment'])->name('appointment.cancel');
Route::post('/appointment/reschedule', [PublicController::class, 'rescheduleAppointment'])->name('appointment.reschedule');
Route::get('/appointment/logout', function () {
    session()->forget('manage_appointment_id');
    return redirect()->route('appointment.manage');
})->name('appointment.logout');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Forgot Password
use App\Http\Controllers\Auth\StaffPasswordResetController;
Route::get('/staff/forgot-password', [StaffPasswordResetController::class, 'showForgotForm'])->name('staff.password.request');
Route::post('/staff/forgot-password/otp', [StaffPasswordResetController::class, 'sendResetOtp'])->name('staff.password.send-otp');
Route::post('/staff/reset-password', [StaffPasswordResetController::class, 'resetPassword'])->name('staff.password.reset');

// Heartbeat (Presence System) — JS pings this every 60s to keep user "Present"
Route::post('/heartbeat', [\App\Http\Controllers\HeartbeatController::class, 'ping'])
    ->middleware('auth')
    ->name('heartbeat.ping');

// Front Desk / Information Desk Routes
use App\Http\Controllers\RegistrationController;
use App\Http\Middleware\RoleMiddleware;

Route::prefix('frontdesk')->middleware(['auth', RoleMiddleware::class . ':admin,information_desk'])->group(function () {
    Route::get('/registration', [RegistrationController::class, 'index'])->name('frontdesk.registration.index');
    Route::get('/registration/search', [RegistrationController::class, 'searchJson'])->name('frontdesk.registration.search');
    Route::post('/patients', [RegistrationController::class, 'storePatient'])->name('frontdesk.patients.store');
    Route::put('/patients/{patient}', [RegistrationController::class, 'updatePatient'])->name('frontdesk.patients.update');
    Route::post('/patients/{patient}/visits', [RegistrationController::class, 'storeVisit'])->name('frontdesk.visits.store');
    Route::get('/queue-slip/{visit}', [RegistrationController::class, 'queueSlip'])->name('frontdesk.queue-slip');
    Route::get('/queue-overview', [\App\Http\Controllers\FrontDeskController::class, 'queueOverview'])->name('frontdesk.queue-overview');
    Route::post('/appointments/{appointment}/check-in', [RegistrationController::class, 'checkIn'])->name('frontdesk.appointments.check-in');
    
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\FrontDeskController::class, 'dashboard'])->name('frontdesk.dashboard');
    Route::get('/api/appointments', [\App\Http\Controllers\FrontDeskController::class, 'getAppointments'])->name('frontdesk.api.appointments');

    // Patient Master List
    Route::get('/patients', [\App\Http\Controllers\PatientController::class, 'index'])->name('frontdesk.patients.index');
    Route::get('/patients/{patient}', [\App\Http\Controllers\PatientController::class, 'show'])->name('frontdesk.patients.show');
    // New patient from PreTriage: register + queue in one step
    Route::post('/register-and-queue/{preTriage}', [RegistrationController::class, 'registerAndQueue'])->name('frontdesk.registerAndQueue');
});

// Vitals / Triage Nurse Routes
Route::prefix('triage')->middleware(['auth', RoleMiddleware::class . ':vitals_nurse'])->group(function () {
    Route::get('/dashboard', [TriageController::class, 'dashboard'])->name('triage.dashboard');
    Route::get('/stats', [TriageController::class, 'getStats'])->name('triage.stats');
    Route::get('/search-patient', [TriageController::class, 'searchPatient'])->name('triage.search');
    Route::post('/store', [TriageController::class, 'store'])->name('triage.store');
    Route::post('/cancel/{preTriage}', [TriageController::class, 'cancel'])->name('triage.cancel');
    Route::post('/restore/{preTriage}', [TriageController::class, 'restore'])->name('triage.restore');
});

// Admin Routes
Route::prefix('admin')->middleware(['auth', RoleMiddleware::class . ':admin,super_admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('admin.analytics');
    Route::get('/analytics/chart/{chart}', [AdminController::class, 'apiChartData'])->name('admin.analytics.chart');
    Route::get('/dashboard/stats-data', [AdminController::class, 'apiDashboardStats'])->name('admin.dashboard.statsData');
    Route::get('/analytics/staff-productivity', [AdminController::class, 'apiStaffProductivityData'])->name('admin.analytics.staff-productivity');
    Route::get('/dashboard/stats', [AdminController::class, 'getStats'])->name('admin.dashboard.stats');
    Route::get('/announcements', [AdminController::class, 'announcements'])->name('admin.announcements.index');
    Route::get('/announcements/create', [AdminController::class, 'createAnnouncement'])->name('admin.announcements.create');
    Route::post('/announcements', [AdminController::class, 'storeAnnouncement'])->name('admin.announcements.store');
    Route::get('/announcements/{announcement}/edit', [AdminController::class, 'editAnnouncement'])->name('admin.announcements.edit');
    Route::put('/announcements/{announcement}', [AdminController::class, 'updateAnnouncement'])->name('admin.announcements.update');
    Route::delete('/announcements/{announcement}', [AdminController::class, 'destroyAnnouncement'])->name('admin.announcements.destroy');
    Route::post('/announcements/{announcement}/toggle', [AdminController::class, 'toggleAnnouncementStatus'])->name('admin.announcements.toggle');
    Route::delete('/announcements/images/bulk/delete', [AdminController::class, 'bulkDeleteImages'])->name('admin.announcements.bulk-delete-images');
    Route::delete('/announcements/bulk/delete', [AdminController::class, 'bulkDeleteAnnouncements'])->name('admin.announcements.bulk-delete');
    Route::delete('/announcements/images/{image}', [AdminController::class, 'deleteAnnouncementImage'])->name('admin.announcements.delete-image');
    
    // Staff Management
    Route::get('/staff', [AdminController::class, 'staffIndex'])->name('admin.staff.index');
    Route::post('/staff', [AdminController::class, 'storeStaff'])->name('admin.staff.store');
    Route::delete('/staff/bulk/delete', [AdminController::class, 'bulkDeleteStaff'])->name('admin.staff.bulk-delete');
    Route::put('/staff/{user}', [AdminController::class, 'updateStaff'])->name('admin.staff.update');
    Route::delete('/staff/{user}', [AdminController::class, 'destroyStaff'])->name('admin.staff.destroy');
    Route::post('/staff/{user}/status', [AdminController::class, 'updateStaffStatus'])->name('admin.staff.status');
    Route::post('/staff/{user}/promote', [AdminController::class, 'promoteToAdmin'])->name('admin.staff.promote');

    // Retention Routes
    Route::get('/retention', [AdminController::class, 'retentionIndex'])->name('admin.retention.index');
    Route::post('/retention/bulk/extend', [AdminController::class, 'bulkExtendRetention'])->name('admin.retention.bulk-extend');
    Route::delete('/retention/bulk/delete', [AdminController::class, 'bulkDeleteRetention'])->name('admin.retention.bulk-delete');
    Route::post('/retention/{patient}/extend', [AdminController::class, 'extendRetention'])->name('admin.retention.extend');
    Route::delete('/retention/{patient}/delete', [AdminController::class, 'deleteRetention'])->name('admin.retention.delete');

    // Patient Records (Admin View)
    Route::get('/patients', [AdminController::class, 'patientRecordsIndex'])->name('admin.patients.index');
    Route::get('/patients/{patient}', [AdminController::class, 'showPatient'])->name('admin.patients.show');
    Route::get('/patients/{patient}/print', [AdminController::class, 'printItr'])->name('admin.patients.print');

    // Audit Trail
    Route::get('/audit', [AdminController::class, 'auditLogs'])->name('admin.audit.index');

    // Archive Routes
    Route::get('/archive', [\App\Http\Controllers\ArchiveController::class, 'index'])->name('admin.archive.index');
    Route::post('/archive/{type}/bulk/restore', [\App\Http\Controllers\ArchiveController::class, 'bulkRestore'])->name('admin.archive.bulk-restore');
    Route::delete('/archive/{type}/bulk/force-delete', [\App\Http\Controllers\ArchiveController::class, 'bulkForceDelete'])->name('admin.archive.bulk-force-delete');
    Route::get('/archive/{type}', [\App\Http\Controllers\ArchiveController::class, 'show'])->name('admin.archive.show');
    Route::post('/archive/{type}/{id}/restore', [\App\Http\Controllers\ArchiveController::class, 'restore'])->name('admin.archive.restore');
    Route::delete('/archive/{type}/{id}/force-delete', [\App\Http\Controllers\ArchiveController::class, 'forceDelete'])->name('admin.archive.force-delete');


    // Content Management
    Route::get('/content', [AdminController::class, 'contentIndex'])->name('admin.content.index');
    Route::put('/content', [AdminController::class, 'contentUpdate'])->name('admin.content.update');
});

// Doctor Routes (regular_doctor + pedia_doctor)
use App\Http\Controllers\DoctorController;
Route::prefix('doctor')->middleware(['auth', RoleMiddleware::class . ':regular_doctor,pedia_doctor'])->group(function () {
    Route::get('/dashboard', [DoctorController::class, 'dashboard'])->name('doctor.dashboard');
    Route::get('/consultation/{consultation}/start', [DoctorController::class, 'startConsultation'])->name('doctor.consultation.start');
    Route::post('/consultation/{consultation}/complete', [DoctorController::class, 'completeConsultation'])->name('doctor.consultation.complete');
    Route::get('/patients/{patient}', [DoctorController::class, 'showPatient'])->name('doctor.patients.show');
    Route::post('/consultation/{consultation}/ancillary', [DoctorController::class, 'storeAncillaryRequest'])->name('doctor.ancillary.store');
});

// Medicine API (for autocomplete in prescriptions)
Route::middleware(['auth'])->get('/api/medicines/search', function (\Illuminate\Http\Request $request) {
    if (!$request->q) return response()->json([]);
    return \App\Models\Medicine::where('name', 'like', $request->q . '%')
        ->orWhere('generic_name', 'like', '%' . $request->q . '%')
        ->take(10)
        ->get();
})->name('api.medicines.search');

// Nurse / Clinical Routes (clinical_nurse role)
use App\Http\Controllers\NurseController;
Route::prefix('nurse')->middleware(['auth', RoleMiddleware::class . ':clinical_nurse'])->group(function () {
    Route::get('/dashboard', [NurseController::class, 'dashboard'])->name('nurse.dashboard');
    Route::post('/forward/{consultation}', [NurseController::class, 'forwardToDoctor'])->name('nurse.forward');
    Route::get('/consultation/{consultation}/start', [NurseController::class, 'startConsultation'])->name('nurse.consultation.start');
    Route::post('/consultation/{consultation}/complete', [NurseController::class, 'completeConsultation'])->name('nurse.consultation.complete');
    Route::post('/consultation/{consultation}/ancillary', [NurseController::class, 'storeAncillaryRequest'])->name('nurse.ancillary.store');
});

// Laboratory / Radiology Routes
use App\Http\Controllers\LabController;
Route::prefix('lab')->middleware(['auth', RoleMiddleware::class . ':laboratory,radiology'])->group(function () {
    Route::get('/dashboard', [LabController::class, 'dashboard'])->name('lab.dashboard');
    Route::post('/ancillary/{ancillary}/complete', [LabController::class, 'completeRequest'])->name('lab.ancillary.complete');
});

// Pharmacy Routes
use App\Http\Controllers\PharmacyController;
Route::prefix('pharmacy')->middleware(['auth', RoleMiddleware::class . ':pharmacy'])->group(function () {
    Route::get('/dashboard', [PharmacyController::class, 'dashboard'])->name('pharmacy.dashboard');
    Route::get('/medicines', [PharmacyController::class, 'medicines'])->name('pharmacy.medicines');
});

// Profile Routes
use App\Http\Controllers\ProfileController;
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::post('/profile/email-otp/send', [ProfileController::class, 'sendEmailOtp'])->name('profile.email-otp.send');
    Route::post('/profile/email-otp/verify', [ProfileController::class, 'verifyEmailOtp'])->name('profile.email-otp.verify');
});

// Localization Route
Route::get('locale/{lang}', function ($lang) {
    if (in_array($lang, ['en', 'fil'])) {
        session()->put('locale', $lang);
    }
    return back();
})->name('locale.switch');
