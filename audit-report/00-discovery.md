# Phase 0: System Discovery

## Tech Stack

| Layer | Technology | Evidence |
|-------|-----------|----------|
| Framework | Laravel (PHP) | `composer.json` |
| Database | MySQL / MariaDB | Raw SQL in base migration: `ENGINE=InnoDB`, `COLLATE=utf8mb4_unicode_ci` |
| Frontend | Blade templates + Vite | `vite.config.js`, `resources/views/` |
| Realtime | Laravel Broadcasting (Events) | `QueueUpdated` event broadcast in controllers |
| Auth | Laravel built-in (session-based) | `AuthController.php`, session guards |
| Jobs/Queue | Laravel Queue (database driver) | `create_jobs_table` migration |
| Notifications | Laravel Notifications (database + mail) | `Notifiable` trait, notifications table |
| Email | Laravel Mail (SMTP) | `AppointmentConfirmation`, `OtpMail` Mailables |
| Storage | Local disk + public disk | `Storage::disk('local')`, `Storage::disk('public')` |
| Scheduler | Laravel Console Scheduler | `routes/console.php` — 9 scheduled commands |

## Folder Structure (Key Directories)

```
app/
├── Console/Commands/          # 8 scheduled artisan commands
├── Events/                    # QueueUpdated broadcast event
├── Http/
│   ├── Controllers/           # 17 controllers + 3 subdirectories
│   │   ├── Admin/             # FacilityUnitController
│   │   ├── Auth/              # StaffPasswordResetController
│   │   └── FrontDesk/         # FollowUpController
│   └── Middleware/            # RoleMiddleware
├── Mail/                      # Mailables (appointment, OTP)
├── Models/                    # 22 Eloquent models
├── Notifications/             # CriticalLabResult, NewPrescription
├── Providers/                 # AppServiceProvider (Gates)
├── Rules/                     # SecureImage validation rule
├── Services/                  # DiagnosticCatalogService, ClinicScheduleService
└── Traits/                    # Auditable, InteractsWithPrescriptions, Sterilizable
database/
├── migrations/                # 33 migration files
└── seeders/                   # FacilityUnitSeeder
resources/views/               # 20+ view subdirectories
routes/
├── web.php                    # 336 lines, all routes
└── console.php                # Scheduled commands
```

## Database Tables (from migrations)

| Table | Key Columns / Purpose |
|-------|----------------------|
| `users` | Staff accounts. `role`, `status`, `staff_id`, `schedule_override`, `avatar_path`, `deleted_at` (soft delete) |
| `patients` | Patient records. `patient_id` (RHU-YYYY-NNNNN), `classification`, `expires_at`, `deleted_at` (soft delete) |
| `appointments` | Public booking. `status` enum: pending, approved, rescheduled, cancelled, arrived, triaged, registered, done, no_show |
| `pre_triages` | Vitals station records. `status` enum: waiting, claimed, cancelled, completed |
| `consultations` | Core visit record. `status` varchar: queued, active, awaiting_results, results_ready, completed, done, cancelled |
| `queues` | Queue tracking. `status` varchar: Waiting, Calling, Completed, Cancelled |
| `ancillary_requests` | Lab/Radiology. `status`: Pending, Specimen Collected, In Progress, Done, Cancelled, Rejected |
| `prescriptions` | Rx records. `status`: pending, partially_dispensed, dispensed, cancelled, expired |
| `prescription_items` | Line items. `medicine_id`, `quantity`, `dispensed_quantity` |
| `medicines` | Drug catalog. `is_active`, `category`, `unit` |
| `medicine_batches` | Inventory batches. `status`: active, depleted, disposed. `disposed_at`, `disposal_reason` |
| `inventory_logs` | Stock movement audit trail. `action`, `quantity_changed`, `performed_by` |
| `medical_cases` | Archived case snapshots. `case_number`, `vitals_snapshot` (JSON) |
| `audit_logs` | System-wide audit trail. Immutable (update/delete blocked in model) |
| `announcements` | CMS content. `status`: draft, pending, published. Soft delete. |
| `announcement_images` | Media sections for announcements |
| `services` | Public-facing service descriptions |
| `facility_units` | Health facilities CMS |
| `site_settings` | Key-value system settings |
| `practitioner_schedules` | Staff schedule slots (day_of_week, time_in, time_out) |
| `conversations` / `messages` / `conversation_user` | Inter-staff chat |
| `notifications` | Laravel notifications table |
| `jobs` / `failed_jobs` | Queue system |
| `password_reset_tokens` | Password reset |

## Roles Discovered

Source: `User::STAFF_ID_ROLE_CODES` (User.php L19-30), `RoleMiddleware::homeForRole` (RoleMiddleware.php L14-29)

| Role String | Display Name | Home Route | Staff ID Prefix |
|-------------|-------------|------------|-----------------|
| `super_admin` | Super Admin | `admin.dashboard` | SAD |
| `admin` | Admin | `admin.dashboard` | ADM |
| `regular_doctor` | Regular Doctor | `doctor.dashboard` | DOC |
| `pedia_doctor` | Pediatric Doctor | `doctor.dashboard` | PED |
| `clinical_nurse` | Clinical Nurse | `nurse.dashboard` | NRS |
| `vitals_nurse` | Vitals/Triage Nurse | `triage.dashboard` | VTN |
| `information_desk` | Receptionist/Front Desk | `frontdesk.dashboard` | IFD |
| `laboratory` | Lab Staff (MedTech) | `lab.dashboard` | LAB |
| `radiology` | Radiology Staff (RadTech) | `lab.dashboard` | RAD |
| `pharmacy` | Pharmacist | `pharmacy.dashboard` | PHM |

**No separate "Inventory/Supply staff" role** — inventory management is handled by `pharmacy` role.  
**No "Patient" login role** — patients interact only via public appointment system (session-based, no user account).

## Permission Mechanism

### Backend: Role Middleware + Laravel Gates

1. **`RoleMiddleware`** (`app/Http/Middleware/RoleMiddleware.php` L36-49):
   - Applied per route group via `RoleMiddleware::class.':role1,role2'`
   - Checks `$user->hasRole(...$roles)` → `in_array($this->role, $roles)` (User.php L113-116)
   - Non-matching authenticated users → redirect to their home with warning
   - Unauthenticated users → 404

2. **Laravel Gates** (defined in `AppServiceProvider.php` L39-84):

| Gate | Allowed Roles | Used On |
|------|--------------|---------|
| `view-audit-logs` | `super_admin` | Audit trail page + CSV export |
| `force-delete` | `super_admin` | Force-delete archived records |
| `truncate-archive` | `super_admin` | Bulk truncate archive |
| `promote-admin` | `super_admin` | Promote staff to admin |
| `manage-admins` | `super_admin` | Edit/delete admin accounts |
| `delete-staff` | `super_admin` | Delete staff accounts |
| `delete-announcements` | `super_admin` | Delete announcements |
| `delete-retention` | `super_admin` | Delete patient retention records |
| `manage-facilities` | `super_admin` | CMS facility management |
| `manage-content` | `admin`, `super_admin` | Edit landing page content |

3. **In-controller checks**: Ownership/authority checks (e.g., `$consultation->doctor_id !== $user->id` → abort 403)

### Route Group Access Summary

| Route Prefix | Middleware Roles |
|-------------|-----------------|
| `/frontdesk/*` | `admin`, `information_desk` |
| `/triage/*` | `vitals_nurse` |
| `/admin/*` | `admin`, `super_admin` |
| `/doctor/*` | `regular_doctor`, `pedia_doctor` |
| `/nurse/*` | `clinical_nurse` |
| `/lab/*` | `laboratory`, `radiology` |
| `/pharmacy/*` (read-only) | `pharmacy`, `admin`, `super_admin` |
| `/pharmacy/*` (operational) | `pharmacy`, `super_admin` |
| `/profile/*` | Any authenticated user |
| `/chat/*` | Any authenticated user |
| `/api/notifications/*` | Any authenticated user |
| `/api/medicines/search` | Any authenticated user |
| `/appointment/*` | Public (no auth) |

## Scheduled Commands

| Command | Schedule | Purpose |
|---------|----------|---------|
| `model:prune` | Daily | Prune expired patients (10yr), cancelled appointments (30d) |
| `appointments:process-no-shows` | Daily 23:55 | Auto-mark overdue appointments as no-show |
| `prescriptions:expire-stale` | Daily 23:57 | Auto-expire unclaimed prescriptions |
| `fix:stuck-appointments` | Daily 23:58 | Fix stuck intermediate appointment statuses |
| `app:cleanup-vitals` | Daily 23:59 | Clean up orphaned pre-triage records |
| `appointments:send-reminders` | Daily 08:00 | Email reminders for next-day appointments |
| `staff:auto-logout` | Every 5 min | Auto-logout inactive staff (30 min) |
| `staff:sync-schedule-status` | Every minute | Sync staff presence from schedule |
| `patients:update-classifications` | Daily 00:05 | Auto-update patient age classifications |

## Key Architectural Patterns

1. **Vitals-first workflow**: Patients go through triage (vitals) before registration/queue
2. **Auto-triage routing**: Severity-based auto-assignment to doctor or clinical nurse
3. **FEFO dispensing**: First-Expiry-First-Out batch deduction with row-level locking
4. **Presence system**: Heartbeat-based + schedule-based online status
5. **Audit trail**: Dual system — `Auditable` trait (auto CRUD logging) + explicit `AuditLog::record()` calls
6. **Input sanitization**: `Sterilizable` trait for XSS prevention on model save
7. **File security**: `SecureImage` rule with extension + MIME + magic-byte triple validation
8. **Data encryption**: PhilHealth numbers encrypted at rest (`'encrypted'` cast)
9. **Soft deletes**: Users and Patients use soft deletes; Announcements use soft deletes
10. **Patient data retention**: 10-year auto-expiry via `expires_at` + prunable trait
