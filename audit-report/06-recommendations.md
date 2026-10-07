# Phase 6: Recommendations

---

## Priority Legend

| Priority | Definition |
|----------|-----------|
| **P0 — Critical** | Must fix before production deployment. Patient safety or data loss risk. |
| **P1 — High** | Fix in the next sprint. Significant workflow gap or security issue. |
| **P2 — Medium** | Plan for next release cycle. Operational improvement. |
| **P3 — Low** | Backlog. Nice-to-have improvement. |

---

## R-01: Resolve Appointment Approval Gap

| Field | Value |
|-------|-------|
| **Priority** | P1 — High |
| **References** | L-01, Flow A step 3 |
| **Problem** | `approved` status exists in enum but is never set. Appointments jump from `pending` to `arrived` on check-in. |
| **Recommendation** | **Option A (Simplest)**: Auto-approve on creation — change `storeAppointment` to set `status = 'approved'` instead of `pending`. Remove `pending` from check-in acceptance list. **Option B**: Add admin approval screen with `approveAppointment` endpoint. |
| **Effort** | Option A: 1 hour. Option B: 4-6 hours. |
| **Soft delete note** | N/A |

## R-02: Enforce Main Health Center Appointment Restriction

| Field | Value |
|-------|-------|
| **Priority** | P1 — High |
| **References** | L-02, System Context Rules |
| **Problem** | No backend enforcement that appointments are only for Main Health Center. |
| **Recommendation** | Add `facility_id` to appointments table defaulting to Main Health Center. Add validation in `storeAppointment` that rejects bookings for other facilities. Add informational notice on other facility pages: "Appointments are available only at the Main Health Center." |
| **Effort** | 2-3 hours |

## R-03: Add Expired Batch Auto-Transition

| Field | Value |
|-------|-------|
| **Priority** | P0 — Critical |
| **References** | L-05, User-identified known gap |
| **Problem** | Expired batches remain `active` status. Pharmacists cannot systematically remove/archive expired stock without manual disposal. |
| **Recommendation** | 1. Add `expired` to batch status enum. 2. Create scheduled command `batches:expire` that runs daily and transitions `active` batches with `expiration_date < today()` to `expired`. 3. Add `InventoryLog` entry for each auto-expired batch. 4. Separate `expired` from `disposed` (expired = system-detected, disposed = staff-action with reason). |
| **Effort** | 3-4 hours |
| **Soft delete note** | Use archive pattern: `expired` status preserves the record but removes from active inventory. |

## R-04: Add Batch Disposal Undo (Restore)

| Field | Value |
|-------|-------|
| **Priority** | P2 — Medium |
| **References** | L-07 |
| **Problem** | Accidental batch disposal is irreversible. |
| **Recommendation** | Add `restoreBatch` endpoint (super_admin-only). Restore batch to `active` status, reset quantity from `InventoryLog` trail, clear disposal fields, create audit log entry. |
| **Effort** | 2-3 hours |

## R-05: Fix Super Admin Access to All Modules

| Field | Value |
|-------|-------|
| **Priority** | P1 — High |
| **References** | A-01, A-02 |
| **Problem** | `super_admin` is excluded from frontdesk and triage routes. |
| **Recommendation** | Add `super_admin` to all route group middleware declarations. In `RoleMiddleware`, consider adding a bypass: `if ($user->hasRole('super_admin')) return $next($request);` at the top of the handle method. |
| **Effort** | 30 minutes |

## R-06: Add Lab/Rad Type Guard on Action Endpoints

| Field | Value |
|-------|-------|
| **Priority** | P2 — Medium |
| **References** | A-05 |
| **Problem** | Lab technicians can process radiology requests and vice versa via direct API calls. |
| **Recommendation** | Add type check to all `LabController` action methods: `$expectedType = $user->role === 'radiology' ? 'Radiology' : 'Laboratory'; if ($ancillary->type !== $expectedType) abort(403);` |
| **Effort** | 1 hour |

## R-07: Handle Prescription Cancellation Stock Reversal

| Field | Value |
|-------|-------|
| **Priority** | P2 — Medium |
| **References** | L-10 |
| **Problem** | Cancelling a `partially_dispensed` prescription does not return dispensed stock. |
| **Recommendation** | Add "Return dispensed stock to inventory?" toggle on cancellation form. If selected, create reverse `InventoryLog` entries and increment batch quantities (LIFO — reverse of FEFO). Record as "Stock Return" action. Require super_admin or pharmacy role. |
| **Effort** | 4-6 hours |

## R-08: Add End-of-Day Queue Cleanup

| Field | Value |
|-------|-------|
| **Priority** | P1 — High |
| **References** | Phase 2 — Open-ended records |
| **Problem** | Consultations in `queued`, `active`, or `awaiting_results` from previous days remain open forever. Queue entries stay `Waiting` or `Calling`. |
| **Recommendation** | Add scheduled command `consultations:close-stale` that runs at 23:59: 1. Consultations from previous days in `queued` → `cancelled` (reason: "Auto-cancelled: patient did not arrive"). 2. Consultations in `active` for >12 hours → flag for review (don't auto-cancel active consultations). 3. Queue entries from previous days in `Waiting`/`Calling` → `Cancelled`. |
| **Effort** | 3-4 hours |

## R-09: Add Configurable Low-Stock Threshold Per Medicine

| Field | Value |
|-------|-------|
| **Priority** | P3 — Low |
| **References** | L-09 |
| **Problem** | Hard-coded 20-unit threshold for all medicines. |
| **Recommendation** | Add `low_stock_threshold` column to `medicines` table (default 20). Use in dashboard queries. Allow pharmacist to set per-medicine in the edit medicine form. |
| **Effort** | 2 hours |

## R-10: Add PhilHealth Number Uniqueness Check

| Field | Value |
|-------|-------|
| **Priority** | P2 — Medium |
| **References** | L-20 |
| **Problem** | Duplicate PhilHealth numbers can be registered for different patients. |
| **Recommendation** | Add application-level uniqueness check. Since PhilHealth numbers are encrypted, add a `philhealth_hash` column with a SHA-256 hash of the plaintext number. Create a unique index on the hash column. Check for duplicates on save. |
| **Effort** | 3-4 hours |

## R-11: Add Batch Number Uniqueness Per Medicine

| Field | Value |
|-------|-------|
| **Priority** | P2 — Medium |
| **References** | L-21 |
| **Problem** | Duplicate batch numbers can be created for the same medicine. |
| **Recommendation** | Add unique constraint `UNIQUE(medicine_id, batch_number)` via migration. Add validation check in `addStock`. |
| **Effort** | 1 hour |

## R-12: Add Login Rate Limiting and Account Lockout

| Field | Value |
|-------|-------|
| **Priority** | P1 — High |
| **References** | Phase 4 — Auth Review |
| **Problem** | No rate limiting or lockout on staff login endpoint. |
| **Recommendation** | Add `throttle:5,1` (5 attempts per minute) to login route. Add temporary lockout after 10 failed attempts (15-minute lockout). Log failed login attempts in audit log. |
| **Effort** | 2 hours |

## R-13: Add Referral Module (Future Phase)

| Field | Value |
|-------|-------|
| **Priority** | P3 — Low |
| **References** | Flow F.3 |
| **Problem** | No referral system exists. |
| **Recommendation** | Future module: `Referral` model with `from_consultation_id`, `to_facility`, `reason`, `status`, `referral_letter_path`. Doctor creates referral from consultation page. Print referral letter. Track acceptance/outcome. |
| **Effort** | 2-3 weeks (full module) |

## R-14: Add Notification for Expired/Rejected Prescriptions and Lab Results

| Field | Value |
|-------|-------|
| **Priority** | P2 — Medium |
| **References** | Phase 2 — Missing handoffs |
| **Problem** | Doctor is not notified when: (a) their prescription expires, (b) a lab sample is rejected. |
| **Recommendation** | 1. In `ExpireStalePrescriptions` command, notify the prescribing doctor. 2. In `LabController::rejectRequest`, send notification to attending doctor. 3. Use existing `NewPrescriptionNotification` pattern. |
| **Effort** | 2-3 hours |

## R-15: Wrap Prescription and Consultation Creation in Transactions

| Field | Value |
|-------|-------|
| **Priority** | P2 — Medium |
| **References** | Phase 5 — Transaction Safety |
| **Problem** | Prescription creation in `InteractsWithPrescriptions::recordPrescription` and consultation creation in `storeVisit` are not wrapped in transactions. |
| **Recommendation** | Wrap `recordPrescription` and `notifyPharmacy` in a transaction. Wrap the consultation creation + queue creation + PreTriage update in `storeVisit` in a single transaction (queue number already uses one, but the outer operations do not). |
| **Effort** | 1-2 hours |

## R-16: Add `blood_type` Validation Constraint

| Field | Value |
|-------|-------|
| **Priority** | P3 — Low |
| **References** | Phase 5 — Validation |
| **Problem** | `blood_type` accepts any string up to 10 chars. |
| **Recommendation** | Change to `'blood_type' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-,Unknown'`. |
| **Effort** | 15 minutes |

---

## Implementation Roadmap

### Sprint 1 (P0 + P1) — Immediate Fixes

| # | Task | Effort | Dependencies |
|---|------|--------|-------------|
| 1 | R-03: Expired batch auto-transition command | 3-4 hrs | None |
| 2 | R-05: Super admin access fix | 30 min | None |
| 3 | R-01: Appointment approval resolution | 1 hr | None |
| 4 | R-02: Main Health Center enforcement | 2-3 hrs | None |
| 5 | R-08: End-of-day queue cleanup command | 3-4 hrs | None |
| 6 | R-12: Login rate limiting + lockout | 2 hrs | None |

**Total Sprint 1**: ~12-15 hours

### Sprint 2 (P2) — Operational Improvements

| # | Task | Effort | Dependencies |
|---|------|--------|-------------|
| 7 | R-04: Batch disposal undo | 2-3 hrs | R-03 |
| 8 | R-06: Lab/Rad type guard | 1 hr | None |
| 9 | R-07: Prescription cancellation stock reversal | 4-6 hrs | None |
| 10 | R-10: PhilHealth uniqueness | 3-4 hrs | None |
| 11 | R-11: Batch number uniqueness | 1 hr | None |
| 12 | R-14: Expired/rejected notifications | 2-3 hrs | None |
| 13 | R-15: Transaction wrapping | 1-2 hrs | None |

**Total Sprint 2**: ~15-22 hours

### Sprint 3 (P3) — Enhancements

| # | Task | Effort |
|---|------|--------|
| 14 | R-09: Configurable low-stock threshold | 2 hrs |
| 15 | R-13: Referral module (design only) | 1 week |
| 16 | R-16: Blood type validation | 15 min |

---

## Architectural Notes

### On Soft Delete vs Hard Delete

The system correctly follows the "archive/deactivate over permanent deletion" principle for:
- **Patients**: Soft delete + 10-year retention
- **Staff accounts**: Soft delete
- **Announcements**: Soft delete
- **Medicines**: `is_active` toggle (functional archive)
- **Batches**: `disposed` status (functional archive with reason)

Force-delete (permanent) is Gate-protected to `super_admin` only. This is the correct approach.

### On Status Machine Design

Consider implementing a formal state machine (e.g., `spatie/laravel-model-states` or simple trait) for:
- Appointment status transitions
- Consultation status transitions
- Prescription status transitions

This would centralize transition rules, prevent invalid transitions, and make the status lifecycle self-documenting.
