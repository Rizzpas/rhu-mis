# Phase 4: Access Control & Role Audit

---

## Role-Permission Matrix

### Route-Level Access (from `web.php` middleware declarations)

| Action / Module | `super_admin` | `admin` | `regular_doctor` | `pedia_doctor` | `clinical_nurse` | `vitals_nurse` | `information_desk` | `laboratory` | `radiology` | `pharmacy` | Public |
|----------------|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| **Admin Dashboard** | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Staff Management** | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Delete Staff** | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Manage Admin Accounts** | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Promote to Admin** | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **View Audit Logs** | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Force Delete Records** | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Truncate Archive** | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Manage Facilities** | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Manage Content** | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Delete Announcements** | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Delete Retention Records** | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Announcement CRUD** | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Patient Records (Admin)** | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Patient Archive** | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Print ITR / Ancillary** | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Appointments Mgmt (Admin)** | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Retention Management** | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Front Desk Dashboard** | ✅* | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Patient Registration** | ✅* | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Patient List (Frontdesk)** | ✅* | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Follow-up Tracking** | ✅* | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Triage / Vitals Station** | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Doctor Consultation** | ❌ | ❌ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Nurse Consultation** | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Lab / Radiology Dashboard** | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ | ❌ | ❌ |
| **Lab Print Report** | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ | ❌ | ❌ |
| **Lab View Result File** | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ✅ | ✅ | ❌ | ❌ |
| **Pharmacy Dashboard** | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ |
| **Pharmacy Dispense/Cancel** | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ |
| **Pharmacy Inventory Mgmt** | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ |
| **Medicine Search API** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ |
| **Chat** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ |
| **Profile Settings** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ |
| **Notifications** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ |
| **Appointment Booking** | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |
| **Appointment Management** | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |

> `✅*` — `super_admin` is not explicitly listed in frontdesk middleware (`admin, information_desk`). **This is an access issue** — super_admin cannot access frontdesk routes.

---

## Access Issues Found

### A-01: Super Admin Cannot Access Front Desk Routes

| Field | Value |
|-------|-------|
| **Severity** | 🟠 High |
| **Location** | `routes/web.php` — frontdesk route group middleware: `RoleMiddleware::class.':admin,information_desk'` |
| **Evidence** | The frontdesk routes only allow `admin` and `information_desk` roles. `super_admin` is excluded. Since `super_admin` should have unrestricted access to all modules, this is a gap. |
| **Impact** | Super admin cannot view front desk operations, registration, or patient queues. |
| **Recommendation** | Add `super_admin` to the frontdesk middleware: `':admin,super_admin,information_desk'`. |

### A-02: Triage Routes Exclude Admin and Super Admin

| Field | Value |
|-------|-------|
| **Severity** | 🟡 Medium |
| **Location** | `routes/web.php` — triage route group middleware: `RoleMiddleware::class.':vitals_nurse'` |
| **Evidence** | Only `vitals_nurse` can access triage routes. Admin and super_admin cannot view or monitor the vitals station. |
| **Impact** | Admin cannot observe triage operations or troubleshoot queue issues. |
| **Recommendation** | Add read-only access for `admin, super_admin` to triage dashboard, or create an admin-facing queue monitor. |

### A-03: Admin Can Access Pharmacy Dashboard But Not Operational Routes

| Field | Value |
|-------|-------|
| **Severity** | 🟢 Low (by design) |
| **Location** | `routes/web.php` — two pharmacy route groups |
| **Evidence** | Pharmacy routes are split: read-only group allows `pharmacy, admin, super_admin`, operational group allows `pharmacy, super_admin`. **Admin can view** pharmacy dashboard but **cannot dispense, manage inventory, or cancel prescriptions**. |
| **Impact** | This appears intentional — admin has read-only pharmacy access. |
| **Recommendation** | Document this as a design decision. No action needed. |

### A-04: Lab Result File Access Missing Pharmacy Role

| Field | Value |
|-------|-------|
| **Severity** | 🟢 Low |
| **Location** | `LabController::viewResultFile` L500-506 |
| **Evidence** | Authorized roles for viewing diagnostic result files: `super_admin, admin, regular_doctor, pedia_doctor, clinical_nurse, laboratory, radiology`. **Pharmacy is excluded.** |
| **Impact** | Pharmacist cannot view lab results that might be relevant for drug interaction checks. |
| **Recommendation** | Consider adding `pharmacy` to authorized roles if lab results are needed for dispensing decisions. |

### A-05: No Row-Level Ownership Check on Lab Actions

| Field | Value |
|-------|-------|
| **Severity** | 🟡 Medium |
| **Location** | `LabController::collectSpecimen`, `startProcessing`, `completeRequest`, etc. |
| **Evidence** | Lab actions (collect specimen, start processing, complete, reject) only check that the user has `laboratory` or `radiology` role. There is no check that the ancillary request `type` matches the user's role (e.g., a `laboratory` user could process a `Radiology` request). |
| **Impact** | Lab technicians could process radiology requests and vice versa. The dashboard filters by type (L17: `$type = $user->role === 'radiology' ? 'Radiology' : 'Laboratory'`), so the UI only shows relevant requests, but the action endpoints are not type-guarded. |
| **Recommendation** | Add `if ($ancillary->type !== $type) abort(403)` to each lab action endpoint. |

### A-06: Nurse Can Create Ancillary Requests and Prescriptions

| Field | Value |
|-------|-------|
| **Severity** | 🟢 Low (by design) |
| **Location** | `NurseController` uses `InteractsWithPrescriptions` trait |
| **Evidence** | Clinical nurses can order lab tests (`storeAncillaryRequest`) and create prescriptions (`completeConsultation`) for consultations assigned to them. This is by design for the nurse practitioner workflow. |
| **Impact** | Nurses have prescriptive authority within the system. This should be validated against local health regulations. |
| **Recommendation** | Verify this aligns with Philippine DOH regulations for RHU clinical nurses. Add a counter-signature workflow if nurse prescriptions require physician co-sign. |

### A-07: Prescription Cancel by Prescriber — Ownership Check Correct

| Field | Value |
|-------|-------|
| **Severity** | 🟢 Positive Finding |
| **Location** | `PharmacyController::cancelByPrescriber` L1229 |
| **Evidence** | `if ($prescription->doctor_id !== $user->id) abort(403, 'You can only cancel prescriptions you created.')` — Correct ownership check. Only the original prescriber can cancel via this endpoint. |
| **Impact** | None — correctly implemented. |

### A-08: Admin Can Delete Any Patient Record (Soft Delete)

| Field | Value |
|-------|-------|
| **Severity** | 🟡 Medium |
| **Location** | `AdminController` — patient archive routes |
| **Evidence** | Admin can soft-delete patients. Only super_admin can force-delete (permanent). This follows the "archive/deactivate over permanent deletion" principle. |
| **Impact** | Admin soft-delete is reversible via `ArchiveController::restore`. Acceptable design. |
| **Recommendation** | No action needed. Gate-protected force-delete is correct. |

---

## Privilege Escalation Vectors

| Vector | Risk | Mitigation |
|--------|------|-----------|
| Staff self-role-change | Low | `role` is not in User `$fillable` for profile updates. Profile controller only allows name, email, password, avatar. |
| Admin creates super_admin | Low | `promote-admin` Gate requires `super_admin`. Admin can create staff but cannot assign `super_admin` role. |
| Direct route access | Low | `RoleMiddleware` blocks unauthorized routes at kernel level. Returns redirect (not data) for authed users, 404 for unauthed. |
| Parameter tampering (doctor_id) | Medium | Most consultation actions check ownership (`$consultation->doctor_id === $user->id`). Lab actions do NOT check type ownership (see A-05). |
| IDOR on patient records | Low | Patient routes use `patient_id` (custom format), not auto-increment `id`. Slightly harder to guess but not a security boundary. |

---

## Authentication Mechanism Review

| Feature | Status | Evidence |
|---------|--------|----------|
| Password hashing | ✅ bcrypt | Laravel default |
| Session-based auth | ✅ | Standard Laravel auth |
| CSRF protection | ✅ | Laravel default middleware |
| Rate limiting (login) | ❓ Not found | No explicit rate limit on `/login` endpoint |
| Rate limiting (OTP) | ✅ | 3/hour rate limit (AppServiceProvider L86-88) |
| Rate limiting (booking) | ✅ | 50/day/IP (AppServiceProvider L90-92) |
| Password reset | ✅ | `StaffPasswordResetController` exists |
| Auto-logout (inactivity) | ✅ | `staff:auto-logout --minutes=30` every 5 min |
| Schedule-based status sync | ✅ | `staff:sync-schedule-status` every minute |
| Multi-factor auth | ❌ Not implemented | No 2FA for staff login |
| Account lockout | ❌ Not found | No lockout after failed login attempts |
| Password complexity | ❓ Needs verification | Depends on validation rules in AuthController |
