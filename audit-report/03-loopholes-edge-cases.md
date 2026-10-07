# Phase 3: Loopholes and Edge Cases

---

## Severity Legend

| Severity | Impact |
|----------|--------|
| 🔴 **Critical** | Data loss, patient safety, or security vulnerability |
| 🟠 **High** | Workflow breakage or significant data inconsistency |
| 🟡 **Medium** | Operational nuisance or minor data quality issue |
| 🟢 **Low** | Cosmetic or minor UX issue |

---

## L-01: Appointment Approval Gap

| Field | Value |
|-------|-------|
| **Classification** | Incomplete |
| **Severity** | 🟠 High |
| **Location** | `routes/web.php`, `PublicController`, `RegistrationController` |
| **Evidence** | Appointment status enum includes `approved` (base migration L97) but no controller endpoint transitions `pending` → `approved`. The `checkIn` method (RegistrationController L127) accepts `pending`, `approved`, and `rescheduled` for check-in, implying `pending` patients can be checked in without approval. |
| **Impact** | All appointments are implicitly "auto-approved" because front desk can check in `pending` appointments. The `approved` status is dead — never set. This means the appointment workflow has no gating step between patient booking and arrival. |
| **Recommendation** | Either (a) add an explicit approval endpoint for staff, or (b) auto-set `approved` on creation and document that all bookings are auto-approved, or (c) remove `approved` from the enum entirely. |

## L-02: Appointments Available for Non-Main-Health-Center Facilities

| Field | Value |
|-------|-------|
| **Classification** | Loophole |
| **Severity** | 🟠 High |
| **Location** | `PublicController::createAppointment` (L337-339), appointment Blade views, `routes/web.php` |
| **Evidence** | The appointment form at `/appointment` does not filter by facility. `FacilityUnit` model exists with multiple facilities (seeded), but the appointment booking form has no facility selector, and no backend validation restricts appointments to the Main Health Center only. The booking flow implicitly books for the Main Health Center but this is not enforced. Other facility unit pages (e.g., `/units/birthing-facility`) show information but have no "Book" button — however, there is no backend guard preventing a crafted POST to book for another facility. |
| **Impact** | Per system context rules, appointments should ONLY be available for the Main Health Center. While the UI doesn't present booking for other facilities, there's no backend enforcement. |
| **Recommendation** | Add a backend validation check that rejects appointment bookings referencing non-Main-Health-Center facilities. Add a clear "Appointments available only at Main Health Center" notice on other facility pages. |

## L-03: No Race Condition Protection on Appointment Slot Booking

| Field | Value |
|-------|-------|
| **Classification** | Loophole |
| **Severity** | 🟡 Medium |
| **Location** | `PublicController::storeAppointment`, `PublicController::checkAvailability` |
| **Evidence** | Slot availability is checked via `checkAvailability` (AJAX, read-only), then the appointment is created in `storeAppointment`. There is no `lockForUpdate()` or atomic slot reservation between the check and the insert. Two patients could book the last slot simultaneously. |
| **Impact** | Double-booking of the same time slot is theoretically possible under concurrent load. Mitigated by the rate limiter (50/day/IP) and low traffic volume typical of an RHU. |
| **Recommendation** | Wrap slot availability check + insert in a DB transaction with pessimistic locking, or use an atomic `INSERT ... WHERE slot_count < max` pattern. |

## L-04: Consultation `done` vs `completed` — Dead Status

| Field | Value |
|-------|-------|
| **Classification** | Loophole |
| **Severity** | 🟡 Medium |
| **Location** | Multiple controllers use `whereIn(['completed', 'done', ...])` |
| **Evidence** | The `done` status is referenced in at least 15 query conditions across `DoctorController`, `NurseController`, `RegistrationController`, and `LabController`. However, **no code ever sets** `consultation.status = 'done'`. The only terminal setter is `'completed'` (DoctorController L284, NurseController). |
| **Impact** | If the DB contained a consultation with `status = 'done'` (e.g., from manual DB edit or legacy data), it would appear in some views but not others, causing inconsistent display. Currently safe because no code creates it, but it's a maintenance hazard. |
| **Recommendation** | Remove `done` from all query conditions, or add a migration to rename `done` → `completed` in existing data. Choose a single terminal status name. |

## L-05: Expired Batches Remain `active` Status — No Auto-Disposal

| Field | Value |
|-------|-------|
| **Classification** | Loophole |
| **Severity** | 🟠 High |
| **Location** | `PharmacyController::dashboard` L41-43, `PharmacyController::dispense` L246, `MedicineBatch` model |
| **Evidence** | Expired batches are excluded from dispensing by a `whereDate('expiration_date', '>=', today())` filter. However, the batch `status` remains `active` even after expiration. The dashboard counts expired batches (`$expiredBatchesCount`) but takes no action. There is no scheduled command to auto-mark expired batches as `disposed` or a new `expired` status. |
| **Impact** | (1) Batch status is misleading — an `active` batch with past expiry date looks "available" at the DB level. (2) Reports based on `status` alone (not date filters) will include expired stock in "active" counts. (3) This is the **known gap identified by the user** — expired/OOS batches cannot be properly removed. |
| **Recommendation** | Add a `status = 'expired'` value and a scheduled command (`batches:expire`) that runs daily to transition `active` batches past their expiration date to `expired`. The `disposeBatch` action handles physical disposal separately. |

## L-06: Out-of-Stock Batches With qty=0 Remain in Active View

| Field | Value |
|-------|-------|
| **Classification** | Loophole |
| **Severity** | 🟡 Medium |
| **Location** | `PharmacyController::medicines` L496-561 |
| **Evidence** | Batches with `quantity = 0` and `status = 'depleted'` are still loaded via `Medicine::with(['batches' => ...])` and displayed in the medicine detail view. There is no "hide depleted batches" filter, and they clutter the batch list. The `dispensing` flow correctly sets depleted batches to `status = 'depleted'`, but they're never archived or hidden by default. |
| **Impact** | UI clutter and confusion for pharmacists when viewing batch lists for medicines with many historical batches. |
| **Recommendation** | Add a batch status filter defaulting to "active" in the medicines view. Consider auto-archiving depleted batches after a configurable retention period. |

## L-07: No Undo for Batch Disposal

| Field | Value |
|-------|-------|
| **Classification** | Incomplete |
| **Severity** | 🟡 Medium |
| **Location** | `PharmacyController::disposeBatch` L914-978 |
| **Evidence** | `disposed` is a terminal status. Once a batch is disposed, there is no restore/undo endpoint. If a pharmacist accidentally disposes a valid batch, the stock is permanently lost from the system. |
| **Impact** | Accidental disposal requires manual database intervention to correct. |
| **Recommendation** | Add a `restoreBatch` endpoint (super_admin only) that reverses disposal: resets `status` to `active`, restores quantity from `original_quantity` or the deducted InventoryLog, and creates an audit trail. |

## L-08: Medicine Search API Accessible to All Authenticated Users

| Field | Value |
|-------|-------|
| **Classification** | Access Issue |
| **Severity** | 🟡 Medium |
| **Location** | `routes/web.php` — `/api/medicines/search` route group uses only `auth` middleware |
| **Evidence** | The medicine search endpoint is protected by `auth` middleware alone, without role restriction. This means a `vitals_nurse`, `information_desk`, or any authenticated staff can query the full medicine catalog with stock levels. |
| **Impact** | Information disclosure — non-pharmacy/clinical staff can see inventory details. Low security risk in an RHU context but violates least-privilege principle. |
| **Recommendation** | Restrict to `pharmacy`, `regular_doctor`, `pedia_doctor`, `clinical_nurse`, `admin`, `super_admin`. |

## L-09: Hard-Coded Low Stock Threshold

| Field | Value |
|-------|-------|
| **Classification** | Loophole |
| **Severity** | 🟢 Low |
| **Location** | `PharmacyController::dashboard` L73, `PharmacyController::medicines` L534-535 |
| **Evidence** | Low stock threshold is hard-coded as `20` units: `->having('active_stock', '<', 20)`. Different medicines have vastly different consumption rates (e.g., paracetamol vs. specialty drugs). |
| **Impact** | Over-alerting for slow-moving items, under-alerting for high-demand items. |
| **Recommendation** | Add a `low_stock_threshold` column to `medicines` table with a default of 20. Use `medicine.low_stock_threshold` in queries. |

## L-10: Prescription Cancellation Does Not Reverse Already-Dispensed Stock

| Field | Value |
|-------|-------|
| **Classification** | Loophole |
| **Severity** | 🟠 High |
| **Location** | `PharmacyController::cancel` L1135-1213 |
| **Evidence** | When cancelling a `partially_dispensed` prescription, the code requires `acknowledge_dispensed_stock` checkbox but **does not reverse the already-dispensed quantities back to inventory**. The dispensed stock is permanently deducted. |
| **Impact** | If a prescription is cancelled after partial dispensing, the dispensed stock is gone from inventory but the prescription is `cancelled`. The pharmacist must manually adjust batch quantities to reconcile. |
| **Recommendation** | Add a "return to stock" option on cancellation of partially-dispensed prescriptions. Create reverse InventoryLog entries and increment batch quantities. |

## L-11: Concurrent Dispensing Race on Same Prescription

| Field | Value |
|-------|-------|
| **Classification** | Loophole |
| **Severity** | 🟡 Medium |
| **Location** | `PharmacyController::dispense` L268-376 |
| **Evidence** | The dispense method uses `lockForUpdate()` on prescription items and batches inside the transaction. **This is correctly implemented.** However, the pre-flight stock check (L227-264) runs **outside** the transaction and does not use locks. A concurrent request could pass pre-flight checks, then fail inside the transaction. |
| **Impact** | The inner transaction correctly handles this with a re-check under lock (L291-298), so no actual data corruption occurs. The user experience is slightly degraded: a pharmacist could see "stock available" in the UI, submit, then get an error. |
| **Recommendation** | This is acceptable. The transaction-level re-check is the correct pattern. Consider adding optimistic locking (version column) for better UX. |

## L-12: No CSRF Protection on Public Appointment Cancel/Reschedule

| Field | Value |
|-------|-------|
| **Classification** | Access Issue |
| **Severity** | 🟡 Medium |
| **Location** | `routes/web.php` — public appointment management routes |
| **Evidence** | Public appointment management uses session-based authentication (reference number + email → OTP). After session is established, cancel/reschedule endpoints use standard POST with CSRF. **CSRF is present** via Laravel's default middleware. However, the session itself is only gated by email+reference number verification, not tied to a user account. |
| **Impact** | If an attacker knows a patient's email and reference number, they can manage (cancel/reschedule) the appointment. The OTP adds a second factor, but OTP sessions persist. |
| **Recommendation** | Add session expiry for appointment management sessions (e.g., 30 minutes). Consider adding rate limiting on the appointment management login endpoint. |

## L-13: PreTriage Duplicate Check Is Incomplete for New Patients

| Field | Value |
|-------|-------|
| **Classification** | Loophole |
| **Severity** | 🟡 Medium |
| **Location** | `TriageController::store` L220-248 |
| **Evidence** | Duplicate checking for existing patients (`patient_id` present) correctly checks for waiting PreTriage and active consultations. However, for **new patients** (no `patient_id`), the duplicate check only looks at the `patients` table by first_name + last_name + dob (L206-217). It does NOT check if the same new-patient name/dob already exists in the `pre_triages` table from the same day. |
| **Impact** | A vitals nurse could accidentally record vitals twice for the same new patient (different PreTriage entries with the same name), creating duplicate queue entries. |
| **Recommendation** | Add a PreTriage duplicate check for new patients: `PreTriage::where('patient_name', 'like', ...)->whereDate('created_at', today())->where('status', 'waiting')`. |

## L-14: Consultation Start Does Not Verify Same-Day

| Field | Value |
|-------|-------|
| **Classification** | Loophole |
| **Severity** | 🟢 Low |
| **Location** | `DoctorController::startConsultation` |
| **Evidence** | A doctor can start a consultation that was queued on a previous day. There is no date check preventing a doctor from processing yesterday's queue. |
| **Impact** | Stale queue entries from previous days can be accidentally processed. Low risk because the dashboard typically filters by today. |
| **Recommendation** | Add a warning or confirmation when starting a consultation from a previous day. |

## L-15: No Maximum File Size for Ancillary Result Uploads (Beyond 10MB)

| Field | Value |
|-------|-------|
| **Classification** | Loophole |
| **Severity** | 🟢 Low |
| **Location** | `LabController::completeRequest` L183 |
| **Evidence** | File upload validation: `'result_file' => ['nullable', 'file', 'max:10240', new SecureImage]`. The 10MB limit is reasonable. The `SecureImage` rule correctly validates extension, MIME type, and magic bytes. **This is well-implemented.** |
| **Impact** | None — this is a positive finding. |
| **Recommendation** | No action needed. The triple validation (extension + MIME + magic bytes) is best practice. |

## L-16: FacilityUnit Auto-Seeding in Production Controller

| Field | Value |
|-------|-------|
| **Classification** | Loophole |
| **Severity** | 🟡 Medium |
| **Location** | `PublicController::units` L73-75, `PublicController::showUnit` L84-86 |
| **Evidence** | `if (FacilityUnit::count() === 0) { (new FacilityUnitSeeder())->run(); }` — If the facility_units table is empty (e.g., after a fresh migration without seeding), the controller auto-runs the seeder on every public page load. |
| **Impact** | (1) Seeder runs in request context (slow). (2) Race condition if two users hit the page simultaneously. (3) Couples production code to seeder. (4) After `manage-facilities` gate allows super_admin to delete all facilities, visiting `/units` re-seeds them. |
| **Recommendation** | Remove auto-seeding from the controller. Add seeder to migration or deployment script. Handle empty state gracefully in the view. |

## L-17: Empty Pediatric Classification Block

| Field | Value |
|-------|-------|
| **Classification** | Incomplete |
| **Severity** | 🟢 Low |
| **Location** | `RegistrationController::storePatient` L466-467 |
| **Evidence** | `if ($validated['classification'] === 'Pediatric') { }` — Empty conditional block. Appears to be a stub for pediatric-specific registration logic that was never implemented. |
| **Impact** | No functional impact currently, but indicates potentially missing pediatric-specific logic (e.g., mandatory guardian info validation is handled elsewhere). |
| **Recommendation** | Remove the empty block or add a `// No additional logic needed — guardian fields are validated above` comment. |

## L-18: Pharmacist Notes Overwritten on Cumulative Dispense

| Field | Value |
|-------|-------|
| **Classification** | Loophole |
| **Severity** | 🟡 Medium |
| **Location** | `PharmacyController::dispense` L358-363 |
| **Evidence** | On each dispense action, `pharmacist_notes` is set to the latest submitted value: `'pharmacist_notes' => $validated['pharmacist_notes'] ?? null`. For partially-dispensed prescriptions where the pharmacist dispenses in multiple rounds, the notes from the first round are **overwritten** by the second round's notes. |
| **Impact** | Loss of pharmacist documentation from earlier dispensing rounds. |
| **Recommendation** | Append notes with timestamp instead of overwriting, or store dispense-round notes in a separate `dispense_logs` table. |

## L-19: Cancellation Reason Allows Empty After Sentinel Stripping

| Field | Value |
|-------|-------|
| **Classification** | Loophole |
| **Severity** | 🟢 Low |
| **Location** | `PharmacyController::cancel` L1177-1188 |
| **Evidence** | The cancellation reason dropdown may contain `__other__` sentinel. If `__other__` is selected but `cancellation_note` is empty/whitespace, the final `$reason` becomes empty string, which is then caught by the `if ($reason === '')` check at L1186. **This is correctly handled** — returns error. |
| **Impact** | None — positive finding. The empty reason case is properly guarded. |
| **Recommendation** | No action needed. |

## L-20: Patient PhilHealth Uniqueness Not Enforced

| Field | Value |
|-------|-------|
| **Classification** | Loophole |
| **Severity** | 🟡 Medium |
| **Location** | `RegistrationController::storePatient` L423-453, `patients` migration |
| **Evidence** | The `philhealth_number` column is not unique in the database schema (no unique index). The registration validation does not check for duplicate PhilHealth numbers. Since PhilHealth numbers are encrypted at rest (`'encrypted'` cast), a database-level unique index on the encrypted column would not work for functional uniqueness. |
| **Impact** | Two different patient records could have the same PhilHealth number, causing billing/insurance issues. |
| **Recommendation** | Add application-level uniqueness check in `storePatient` and `updatePatient`: decrypt and compare PhilHealth numbers before saving. Or add a hashed index column for lookups. |

## L-21: Batch Number Not Unique Per Medicine

| Field | Value |
|-------|-------|
| **Classification** | Loophole |
| **Severity** | 🟡 Medium |
| **Location** | `PharmacyController::addStock` L859-889, `medicine_batches` migration |
| **Evidence** | The `batch_number` column has no uniqueness constraint (neither globally nor per medicine_id). A pharmacist can add two batches with the same batch number for the same medicine. The `normalizeBatchNumber` method standardizes the format but does not check for duplicates. |
| **Impact** | Duplicate batch numbers make it impossible to distinguish batches in reports and audit trails. |
| **Recommendation** | Add a unique constraint on `(medicine_id, batch_number)` and validate in `addStock`. |

## L-22: No Input Sanitization on Chat Messages

| Field | Value |
|-------|-------|
| **Classification** | Access Issue |
| **Severity** | 🟡 Medium |
| **Location** | `ChatController::sendMessage` |
| **Evidence** | Chat messages are validated as `'body' => 'required|string|max:2000'` but there is no explicit XSS sanitization. The `Sterilizable` trait is used on `Patient` model but NOT on the `Message` model. If messages are rendered with `{!! !!}` (unescaped) in Blade, XSS is possible. If rendered with `{{ }}` (escaped), this is safe. |
| **Impact** | Depends on Blade rendering. If escaped (Laravel default), low risk. If unescaped for rich text, high risk. |
| **Recommendation** | Verify Blade templates use `{{ $message->body }}` (escaped). If rich text is needed, add the `Sterilizable` trait to the `Message` model. |

## L-23: Audit Log Immutability Bypass via Mass Assignment

| Field | Value |
|-------|-------|
| **Classification** | Access Issue |
| **Severity** | 🟢 Low |
| **Location** | `AuditLog` model L20-28 |
| **Evidence** | The AuditLog model blocks `updating` and `deleting` via model events returning `false`. However, this can be bypassed by: (1) Raw SQL queries, (2) `DB::table('audit_logs')->where(...)→update(...)`, (3) `AuditLog::withoutEvents(fn() => ...)`. The `$fillable` array allows `user_id`, `action`, `model_type`, `model_id`, `changes`, `ip_address`, `user_agent` — all writable. |
| **Impact** | A developer or super_admin with DB access could tamper with audit logs. In the application context, this is acceptable for a model-level guard. |
| **Recommendation** | For higher assurance, consider database-level triggers or append-only table design. Current implementation is adequate for typical RHU threat model. |

---

## Summary Table

| ID | Classification | Severity | Title |
|----|---------------|----------|-------|
| L-01 | Incomplete | 🟠 High | Appointment approval gap — no `pending` → `approved` transition |
| L-02 | Loophole | 🟠 High | No backend restriction of appointments to Main Health Center |
| L-03 | Loophole | 🟡 Medium | No atomic slot reservation for appointment booking |
| L-04 | Loophole | 🟡 Medium | Dead `done` status on consultations |
| L-05 | Loophole | 🟠 High | Expired batches remain `active` — no auto-disposal |
| L-06 | Loophole | 🟡 Medium | Depleted batches clutter active view |
| L-07 | Incomplete | 🟡 Medium | No undo for accidental batch disposal |
| L-08 | Access Issue | 🟡 Medium | Medicine search API open to all authenticated users |
| L-09 | Loophole | 🟢 Low | Hard-coded low stock threshold (20 units) |
| L-10 | Loophole | 🟠 High | Prescription cancellation doesn't reverse dispensed stock |
| L-11 | Loophole | 🟡 Medium | Pre-flight stock check outside transaction (correctly handled inside) |
| L-12 | Access Issue | 🟡 Medium | Appointment management session has no expiry |
| L-13 | Loophole | 🟡 Medium | PreTriage duplicate check incomplete for new patients |
| L-14 | Loophole | 🟢 Low | No same-day check on consultation start |
| L-15 | — | 🟢 Positive | File upload triple validation is well-implemented |
| L-16 | Loophole | 🟡 Medium | Auto-seeding in production controller |
| L-17 | Incomplete | 🟢 Low | Empty Pediatric classification block |
| L-18 | Loophole | 🟡 Medium | Pharmacist notes overwritten on cumulative dispense |
| L-19 | — | 🟢 Positive | Cancellation reason empty case properly guarded |
| L-20 | Loophole | 🟡 Medium | PhilHealth number uniqueness not enforced |
| L-21 | Loophole | 🟡 Medium | Batch number not unique per medicine |
| L-22 | Access Issue | 🟡 Medium | No XSS sanitization on chat messages |
| L-23 | Access Issue | 🟢 Low | Audit log model-level immutability can be bypassed |
