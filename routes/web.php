<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
use App\Http\Controllers\TriageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'index'])->name('welcome');
Route::get('/about', [PublicController::class, 'about'])->name('about');
Route::get('/units', [PublicController::class, 'units'])->name('units.index');
Route::get('/units/{slug}', [PublicController::class, 'showUnit'])->name('units.show');
Route::get('/check-home-updates', [PublicController::class, 'checkHomeUpdates'])->name('welcome.check-updates');
Route::get('/announcements', [PublicController::class, 'announcementsIndex'])->name('announcements.index');
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
    session()->regenerateToken(); // Prevent CSRF token reuse across contexts

    return redirect()->route('appointment.manage');
})->name('appointment.logout');

// Developer Preview Route for Toast Notifications (Local only)
if (app()->environment('local')) {
    Route::get('/dev/toast-preview', function () {
        return view('dev.toast-preview');
    })->name('dev.toast-preview');
}

// Auth Routes
// Hidden staff portal entry — sets a session token so the login page will render.
// This route is triggered from a disguised link in the public footer.
Route::get('/staff-access', function () {
    session(['staff_portal_token' => true]);
    return redirect()->route('login');
})->name('staff.access');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Forgot Password
use App\Http\Controllers\Auth\StaffPasswordResetController;

Route::get('/staff-access/forgot-password', function () {
    session(['staff_portal_token' => true]);
    return redirect()->route('staff.password.request');
})->name('staff.password.access');

Route::get('/staff/forgot-password', [StaffPasswordResetController::class, 'showForgotForm'])->name('staff.password.request');
Route::post('/staff/forgot-password/otp', [StaffPasswordResetController::class, 'sendResetOtp'])->middleware('throttle:3,1')->name('staff.password.send-otp');
Route::post('/staff/reset-password', [StaffPasswordResetController::class, 'resetPassword'])->name('staff.password.reset');

// Heartbeat (Presence System) — JS pings this every 60s to keep user "Present"
Route::post('/heartbeat', [\App\Http\Controllers\HeartbeatController::class, 'ping'])
    ->middleware('auth')
    ->name('heartbeat.ping');

// Notifications API
use App\Http\Controllers\NotificationController;

Route::middleware('auth')->group(function () {
    Route::get('/api/notifications', [NotificationController::class, 'index']);
    Route::post('/api/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/api/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
});

// Chat API
use App\Http\Controllers\ChatController;

Route::middleware('auth')->prefix('chat')->group(function () {
    Route::get('/conversations', [ChatController::class, 'index'])->name('chat.conversations');
    Route::post('/conversations', [ChatController::class, 'store'])->name('chat.conversations.store');
    Route::get('/conversations/{conversation}/messages', [ChatController::class, 'messages'])->name('chat.messages');
    Route::post('/conversations/{conversation}/messages', [ChatController::class, 'sendMessage'])->name('chat.messages.send');
    Route::post('/conversations/{conversation}/read', [ChatController::class, 'markAsRead'])->name('chat.read');
    Route::get('/users', [ChatController::class, 'users'])->name('chat.users');
    Route::get('/unread-count', [ChatController::class, 'unreadCount'])->name('chat.unread-count');
});

// Front Desk / Information Desk Routes
use App\Http\Controllers\RegistrationController;
use App\Http\Middleware\RoleMiddleware;

Route::prefix('frontdesk')->middleware(['auth', RoleMiddleware::class.':admin,information_desk'])->group(function () {
    Route::get('/registration', [RegistrationController::class, 'index'])->name('frontdesk.registration.index');
    Route::get('/registration/search', [RegistrationController::class, 'searchJson'])->name('frontdesk.registration.search');
    Route::get('/registration/queue-json', [RegistrationController::class, 'queueJson'])->name('frontdesk.registration.queue-json');
    Route::post('/patients', [RegistrationController::class, 'storePatient'])->name('frontdesk.patients.store');
    Route::put('/patients/{patient}', [RegistrationController::class, 'updatePatient'])->name('frontdesk.patients.update');
    Route::post('/patients/{patient}/visits', [RegistrationController::class, 'storeVisit'])->name('frontdesk.visits.store');
    Route::get('/queue-slip/{visit}', [RegistrationController::class, 'queueSlip'])->name('frontdesk.queue-slip');
    Route::get('/queue-overview', [\App\Http\Controllers\FrontDeskController::class, 'queueOverview'])->name('frontdesk.queue-overview');
    Route::post('/appointments/{appointment}/check-in', [RegistrationController::class, 'checkIn'])->name('frontdesk.appointments.check-in');
    Route::post('/appointments/{appointment}/cancel', [RegistrationController::class, 'cancelAppointment'])->name('frontdesk.appointments.cancel');
    Route::post('/appointments/{appointment}/no-show', [RegistrationController::class, 'markNoShow'])->name('frontdesk.appointments.no-show');

    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\FrontDeskController::class, 'dashboard'])->name('frontdesk.dashboard');
    Route::get('/api/appointments', [\App\Http\Controllers\FrontDeskController::class, 'getAppointments'])->name('frontdesk.api.appointments');
    Route::get('/api/stats', [\App\Http\Controllers\FrontDeskController::class, 'getStats'])->name('frontdesk.api.stats');

    // Patient Master List
    Route::get('/patients', [\App\Http\Controllers\PatientController::class, 'index'])->name('frontdesk.patients.index');
    Route::get('/patients/{patient}', [\App\Http\Controllers\PatientController::class, 'show'])->name('frontdesk.patients.show');
    // New patient from PreTriage: register + queue in one step
    Route::post('/register-and-queue/{preTriage}', [RegistrationController::class, 'registerAndQueue'])->name('frontdesk.registerAndQueue');
    Route::post('/queue/{consultation}/cancel', [RegistrationController::class, 'cancelQueuedConsultation'])->name('frontdesk.queue.cancel');

    // Follow-Up Management Tracker
    Route::get('/followups', [\App\Http\Controllers\FrontDesk\FollowUpController::class, 'index'])->name('frontdesk.followups.index');
    Route::post('/followups/{consultation}/reschedule', [\App\Http\Controllers\FrontDesk\FollowUpController::class, 'reschedule'])->name('frontdesk.followups.reschedule');
    Route::post('/followups/{consultation}/fulfill', [\App\Http\Controllers\FrontDesk\FollowUpController::class, 'fulfill'])->name('frontdesk.followups.fulfill');
});

// Vitals / Triage Nurse Routes
Route::prefix('triage')->middleware(['auth', RoleMiddleware::class.':vitals_nurse'])->group(function () {
    Route::get('/dashboard', [TriageController::class, 'dashboard'])->name('triage.dashboard');
    Route::get('/stats', [TriageController::class, 'getStats'])->name('triage.stats');
    Route::get('/search-patient', [TriageController::class, 'searchPatient'])->name('triage.search');
    Route::post('/store', [TriageController::class, 'store'])->name('triage.store');
    Route::post('/cancel/{preTriage}', [TriageController::class, 'cancel'])->name('triage.cancel');
    Route::post('/restore/{preTriage}', [TriageController::class, 'restore'])->name('triage.restore');
});

// Admin Routes
Route::prefix('admin')->middleware(['auth', RoleMiddleware::class.':admin,super_admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('admin.analytics');
    Route::get('/analytics/chart/{chart}', [AdminController::class, 'apiChartData'])->name('admin.analytics.chart');
    Route::get('/dashboard/stats-data', [AdminController::class, 'apiDashboardStats'])->name('admin.dashboard.statsData');
    Route::get('/analytics/staff-productivity', [AdminController::class, 'apiStaffProductivityData'])->name('admin.analytics.staff-productivity');
    Route::get('/analytics/export-csv', [AdminController::class, 'exportAnalyticsCsv'])->name('admin.analytics.export-csv');
    Route::get('/analytics/export-summary-csv', [AdminController::class, 'exportAnalyticsSummaryCsv'])->name('admin.analytics.export-summary-csv');
    Route::get('/dashboard/stats', [AdminController::class, 'getStats'])->name('admin.dashboard.stats');
    Route::get('/announcements', [AdminController::class, 'announcements'])->name('admin.announcements.index');
    Route::get('/announcements/create', [AdminController::class, 'createAnnouncement'])->name('admin.announcements.create');
    Route::post('/announcements', [AdminController::class, 'storeAnnouncement'])->name('admin.announcements.store');
    Route::get('/announcements/{announcement}/edit', [AdminController::class, 'editAnnouncement'])->name('admin.announcements.edit');
    Route::put('/announcements/{announcement}', [AdminController::class, 'updateAnnouncement'])->name('admin.announcements.update');
    Route::delete('/announcements/{announcement}', [AdminController::class, 'destroyAnnouncement'])->name('admin.announcements.destroy')->middleware('can:delete-announcements');
    Route::post('/announcements/{announcement}/toggle', [AdminController::class, 'toggleAnnouncementStatus'])->name('admin.announcements.toggle');
    Route::delete('/announcements/images/bulk/delete', [AdminController::class, 'bulkDeleteImages'])->name('admin.announcements.bulk-delete-images');
    Route::delete('/announcements/bulk/delete', [AdminController::class, 'bulkDeleteAnnouncements'])->name('admin.announcements.bulk-delete')->middleware('can:delete-announcements');
    Route::delete('/announcements/images/{image}', [AdminController::class, 'deleteAnnouncementImage'])->name('admin.announcements.delete-image');

    // Staff Management
    Route::get('/staff', [AdminController::class, 'staffIndex'])->name('admin.staff.index');
    Route::post('/staff', [AdminController::class, 'storeStaff'])->name('admin.staff.store');
    Route::delete('/staff/bulk/delete', [AdminController::class, 'bulkDeleteStaff'])->name('admin.staff.bulk-delete')->middleware('can:delete-staff');
    Route::put('/staff/{user}', [AdminController::class, 'updateStaff'])->name('admin.staff.update');
    Route::delete('/staff/{user}', [AdminController::class, 'destroyStaff'])->name('admin.staff.destroy')->middleware('can:delete-staff');
    Route::post('/staff/{user}/status', [AdminController::class, 'updateStaffStatus'])->name('admin.staff.status');
    Route::post('/staff/{user}/promote', [AdminController::class, 'promoteToAdmin'])->name('admin.staff.promote')->middleware('can:promote-admin');

    // Retention Routes
    Route::get('/retention', [AdminController::class, 'retentionIndex'])->name('admin.retention.index');
    Route::post('/retention/bulk/extend', [AdminController::class, 'bulkExtendRetention'])->name('admin.retention.bulk-extend');
    Route::delete('/retention/bulk/delete', [AdminController::class, 'bulkDeleteRetention'])->name('admin.retention.bulk-delete')->middleware('can:delete-retention');
    Route::post('/retention/{patient}/extend', [AdminController::class, 'extendRetention'])->name('admin.retention.extend');
    Route::delete('/retention/{patient}/delete', [AdminController::class, 'deleteRetention'])->name('admin.retention.delete')->middleware('can:delete-retention');

    // Patient Records (Admin View)
    Route::get('/patients', [AdminController::class, 'patientRecordsIndex'])->name('admin.patients.index');
    Route::get('/patients/{patient}', [AdminController::class, 'showPatient'])->name('admin.patients.show');
    Route::get('/patients/{patient}/print', [AdminController::class, 'printItr'])->name('admin.patients.print');
    Route::get('/patients/{patient}/ancillary/print', [AdminController::class, 'printAncillary'])->name('admin.patients.ancillary.print');
    Route::get('/ancillary/{ancillary}/print', [AdminController::class, 'printAncillarySingle'])->name('admin.ancillary.print');

    // Audit Trail (Super Admin only)
    Route::get('/audit', [AdminController::class, 'auditLogs'])->name('admin.audit.index')->middleware('can:view-audit-logs');
    Route::get('/audit/export-csv', [AdminController::class, 'exportAuditLogsCsv'])->name('admin.audit.export-csv')->middleware('can:view-audit-logs');

    // Archive Routes
    Route::get('/archive', [\App\Http\Controllers\ArchiveController::class, 'index'])->name('admin.archive.index');
    Route::post('/archive/truncate-all-year', [\App\Http\Controllers\ArchiveController::class, 'truncateAllOneYear'])->name('admin.archive.truncate-all-year')->middleware('can:truncate-archive');
    Route::post('/archive/{type}/truncate-year', [\App\Http\Controllers\ArchiveController::class, 'truncateCategoryOneYear'])->name('admin.archive.truncate-year')->middleware('can:truncate-archive');
    Route::post('/archive/{type}/bulk/restore', [\App\Http\Controllers\ArchiveController::class, 'bulkRestore'])->name('admin.archive.bulk-restore');
    Route::delete('/archive/{type}/bulk/force-delete', [\App\Http\Controllers\ArchiveController::class, 'bulkForceDelete'])->name('admin.archive.bulk-force-delete')->middleware('can:force-delete');
    Route::get('/archive/{type}', [\App\Http\Controllers\ArchiveController::class, 'show'])->name('admin.archive.show');
    Route::post('/archive/{type}/{id}/restore', [\App\Http\Controllers\ArchiveController::class, 'restore'])->name('admin.archive.restore');
    Route::delete('/archive/{type}/{id}/force-delete', [\App\Http\Controllers\ArchiveController::class, 'forceDelete'])->name('admin.archive.force-delete')->middleware('can:force-delete');

    // Content Management
    Route::get('/content', [AdminController::class, 'contentIndex'])->name('admin.content.index');
    Route::put('/content', [AdminController::class, 'contentUpdate'])->name('admin.content.update');

    // Dynamic Health Facilities / Units CMS (Super Admin only)
    Route::post('/facilities', [\App\Http\Controllers\Admin\FacilityUnitController::class, 'store'])->name('admin.facilities.store')->middleware('can:manage-facilities');
    Route::put('/facilities/{facility}', [\App\Http\Controllers\Admin\FacilityUnitController::class, 'update'])->name('admin.facilities.update')->middleware('can:manage-facilities');
    Route::delete('/facilities/{facility}', [\App\Http\Controllers\Admin\FacilityUnitController::class, 'destroy'])->name('admin.facilities.destroy')->middleware('can:manage-facilities');
});

// Doctor Routes (regular_doctor + pedia_doctor)
use App\Http\Controllers\DoctorController;

Route::prefix('doctor')->middleware(['auth', RoleMiddleware::class.':regular_doctor,pedia_doctor'])->group(function () {
    Route::get('/dashboard', [DoctorController::class, 'dashboard'])->name('doctor.dashboard');
    Route::get('/waiting-results', [DoctorController::class, 'waitingResults'])->name('doctor.waiting-results');
    Route::get('/consultation/{consultation}/start', [DoctorController::class, 'startConsultation'])->name('doctor.consultation.start');
    Route::post('/consultation/{consultation}/complete', [DoctorController::class, 'completeConsultation'])->name('doctor.consultation.complete');
    Route::post('/consultation/{consultation}/addendum', [DoctorController::class, 'storeAddendum'])->name('doctor.consultation.addendum');
    Route::post('/consultation/{consultation}/cancel', [DoctorController::class, 'cancelConsultation'])->name('doctor.consultation.cancel');
    Route::post('/consultation/{consultation}/prescription/cancel', [DoctorController::class, 'cancelByPrescriber'])->name('doctor.prescription.cancel');
    Route::get('/patients/{patient}', [DoctorController::class, 'showPatient'])->name('doctor.patients.show');
    Route::post('/consultation/{consultation}/ancillary', [DoctorController::class, 'storeAncillaryRequest'])->name('doctor.ancillary.store');
    Route::post('/ancillary/{ancillary}/repeat', [DoctorController::class, 'repeatAncillaryRequest'])->name('doctor.ancillary.repeat');
    Route::post('/ancillary/{ancillary}/cancel', [DoctorController::class, 'cancelAncillaryRequest'])->name('doctor.ancillary.cancel');
});

// Medicine API (for autocomplete in prescriptions)
Route::middleware(['auth'])->get('/api/medicines/search', function (\Illuminate\Http\Request $request) {
    if (! $request->q) {
        return response()->json([]);
    }
    $medicines = \App\Models\Medicine::where('name', 'like', $request->q.'%')
        ->orWhere('generic_name', 'like', '%'.$request->q.'%')
        ->take(10)
        ->get()
        ->map(function ($med) {
            $stock = $med->batches()
                ->where('status', '!=', 'disposed')
                ->where('quantity', '>', 0)
                ->whereDate('expiration_date', '>=', today())
                ->sum('quantity');

            return [
                'id' => $med->id,
                'name' => $med->name,
                'generic_name' => $med->generic_name,
                'form' => $med->form,
                'stock' => (int) $stock,
                'in_inventory' => true,
            ];
        });

    return response()->json($medicines);
})->name('api.medicines.search');

// Nurse / Clinical Routes (clinical_nurse role)
use App\Http\Controllers\NurseController;

Route::prefix('nurse')->middleware(['auth', RoleMiddleware::class.':clinical_nurse'])->group(function () {
    Route::get('/dashboard', [NurseController::class, 'dashboard'])->name('nurse.dashboard');
    Route::post('/forward/{consultation}', [NurseController::class, 'forwardToDoctor'])->name('nurse.forward');
    Route::get('/consultation/{consultation}/start', [NurseController::class, 'startConsultation'])->name('nurse.consultation.start');
    Route::post('/consultation/{consultation}/complete', [NurseController::class, 'completeConsultation'])->name('nurse.consultation.complete');
    Route::post('/consultation/{consultation}/addendum', [NurseController::class, 'storeAddendum'])->name('nurse.consultation.addendum');
    Route::post('/consultation/{consultation}/cancel', [NurseController::class, 'cancelConsultation'])->name('nurse.consultation.cancel');
    Route::post('/consultation/{consultation}/prescription/cancel', [NurseController::class, 'cancelByPrescriber'])->name('nurse.prescription.cancel');
    Route::post('/consultation/{consultation}/ancillary', [NurseController::class, 'storeAncillaryRequest'])->name('nurse.ancillary.store');
    Route::post('/ancillary/{ancillary}/repeat', [NurseController::class, 'repeatAncillaryRequest'])->name('nurse.ancillary.repeat');
    Route::post('/ancillary/{ancillary}/cancel', [NurseController::class, 'cancelAncillaryRequest'])->name('nurse.ancillary.cancel');
});

// Laboratory / Radiology Routes
use App\Http\Controllers\LabController;

Route::prefix('lab')->middleware(['auth', RoleMiddleware::class.':laboratory,radiology'])->group(function () {
    Route::get('/dashboard', [LabController::class, 'dashboard'])->name('lab.dashboard');
    Route::post('/ancillary/{ancillary}/collect-specimen', [LabController::class, 'collectSpecimen'])->name('lab.ancillary.collect-specimen');
    Route::post('/ancillary/{ancillary}/start-processing', [LabController::class, 'startProcessing'])->name('lab.ancillary.start-processing');
    Route::post('/ancillary/{ancillary}/complete', [LabController::class, 'completeRequest'])->name('lab.ancillary.complete');
    Route::post('/ancillary/{ancillary}/amend', [LabController::class, 'amendResult'])->name('lab.ancillary.amend');
    Route::post('/ancillary/{ancillary}/reject', [LabController::class, 'rejectRequest'])->name('lab.ancillary.reject');
    Route::post('/ancillary/{ancillary}/cancel', [LabController::class, 'cancelRequest'])->name('lab.ancillary.cancel');
    Route::post('/ancillary/{ancillary}/archive', [LabController::class, 'archiveRequest'])->name('lab.ancillary.archive');
    Route::post('/ancillary/{ancillary}/restore', [LabController::class, 'restoreRequest'])->name('lab.ancillary.restore');
    Route::get('/ancillary/{ancillary}/print', [LabController::class, 'printReport'])->name('lab.ancillary.print');
});

// Pharmacy Routes
use App\Http\Controllers\PharmacyController;

Route::prefix('pharmacy')->middleware(['auth', RoleMiddleware::class.':pharmacy,admin,super_admin'])->group(function () {
    // Read-only routes — Admin can view dashboards, history, and medicine lists
    Route::get('/dashboard', [PharmacyController::class, 'dashboard'])->name('pharmacy.dashboard');
    Route::get('/history', [PharmacyController::class, 'history'])->name('pharmacy.history');
    Route::get('/medicines', [PharmacyController::class, 'medicines'])->name('pharmacy.medicines');
    Route::get('/medicines/export-csv', [PharmacyController::class, 'exportStockCsv'])->name('pharmacy.medicines.export-csv');
    Route::get('/written-off', [PharmacyController::class, 'writtenOff'])->name('pharmacy.written-off');
});

Route::prefix('pharmacy')->middleware(['auth', RoleMiddleware::class.':pharmacy,super_admin'])->group(function () {
    // Operational routes — Only Pharmacy staff and Super Admin can dispense/modify inventory
    Route::post('/dispense/{prescription}', [PharmacyController::class, 'dispense'])->name('pharmacy.dispense');
    Route::post('/prescriptions/{prescription}/cancel', [PharmacyController::class, 'cancel'])->name('pharmacy.prescription.cancel');
    Route::post('/medicines', [PharmacyController::class, 'storeMedicine'])->name('pharmacy.medicines.store');
    Route::put('/medicines/{medicine}', [PharmacyController::class, 'updateMedicine'])->name('pharmacy.medicines.update');
    Route::post('/medicines/{medicine}/add-stock', [PharmacyController::class, 'addStock'])->name('pharmacy.medicines.add-stock');
    Route::post('/medicines/{medicine}/toggle-status', [PharmacyController::class, 'toggleMedicineStatus'])->name('pharmacy.medicines.toggle-status');
    Route::post('/batches/{batch}/dispose', [PharmacyController::class, 'disposeBatch'])->name('pharmacy.batches.dispose');
    Route::post('/batches/{batch}/adjust', [PharmacyController::class, 'adjustStock'])->name('pharmacy.batches.adjust');
});

// Profile Routes
use App\Http\Controllers\ProfileController;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/status', [ProfileController::class, 'updateStatus'])->name('profile.status.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::post('/profile/email-otp/send', [ProfileController::class, 'sendEmailOtp'])->name('profile.email-otp.send');
    Route::post('/profile/email-otp/verify', [ProfileController::class, 'verifyEmailOtp'])->name('profile.email-otp.verify');
});
