# Phase 7: Functional & QA Test Plan

---

## Test Categories

| Category | Description |
|----------|-----------|
| **FLOW** | End-to-end user flow validation |
| **EDGE** | Edge cases and boundary conditions |
| **ACCESS** | Role-based access control verification |
| **DATA** | Data integrity and validation |
| **CONCUR** | Concurrent access / race conditions |
| **REGRESS** | Regression tests for identified loopholes |

---

## FLOW Tests — End-to-End Process Validation

### FLOW-01: Appointment → Consultation → Discharge (Happy Path)

| Step | Action | Expected Result | Validates |
|------|--------|----------------|-----------|
| 1 | Public user books appointment with valid data + OTP | Appointment created with `pending` status, confirmation email sent | Flow A, steps 1-7 |
| 2 | Receptionist checks in the patient | Appointment → `arrived` | Flow A, step 13 |
| 3 | Vitals nurse records vitals via triage form | PreTriage created with `waiting` status, Appointment → `triaged` | Flow A, step 16 |
| 4 | Receptionist registers visit (storeVisit) with severity | Consultation created `queued`, Queue created `Waiting`, PreTriage → `claimed` | Flow A, step 17 |
| 5 | Doctor starts consultation | Consultation → `active`, Queue → `Calling` | Flow A, step 18 |
| 6 | Doctor orders lab test | AncillaryRequest created `Pending`, Consultation → `awaiting_results` | Flow C, step 1 |
| 7 | Lab staff collects specimen | AncillaryRequest → `Specimen Collected` | Flow C, step 2 |
| 8 | Lab staff starts processing | AncillaryRequest → `In Progress` | Flow C, step 3 |
| 9 | Lab staff submits results | AncillaryRequest → `Done`, Consultation → `results_ready` | Flow C, steps 4-5 |
| 10 | Doctor resumes consultation | Consultation → `active` | Flow C, step 6 |
| 11 | Doctor completes consultation with prescription | Consultation → `completed`, Prescription created `pending`, MedicalCase created, Queue → `Completed`, Appointment → `done` | Flow A, step 20 |
| 12 | Pharmacist dispenses prescription | Prescription → `dispensed`, batches decremented, InventoryLog created | Flow D, step 4 |

### FLOW-02: Walk-in New Patient (Happy Path)

| Step | Action | Expected Result | Validates |
|------|--------|----------------|-----------|
| 1 | Vitals nurse records vitals for unnamed patient (no patient_id) | PreTriage created `waiting` with patient_name, no patient_id | Flow B, step 2 |
| 2 | Receptionist uses registerAndQueue for new patient | Patient created (RHU-YYYY-NNNNN), Consultation created, Queue created, PreTriage → `claimed` | Flow B, step 4b |
| 3-onward | Same as FLOW-01 steps 5-12 | | |

### FLOW-03: Walk-in Returning Patient

| Step | Action | Expected Result | Validates |
|------|--------|----------------|-----------|
| 1 | Vitals nurse searches for patient, records vitals with patient_id | PreTriage created with patient_id linked | Flow B, step 3 |
| 2 | Receptionist uses storeVisit for existing patient | Consultation created, Queue created | Flow B, step 4a |
| 3-onward | Same as FLOW-01 steps 5-12 | | |

### FLOW-04: Follow-up Visit Routing

| Step | Action | Expected Result |
|------|--------|----------------|
| 1 | Doctor completes consultation with `is_followup_needed=true`, `followup_date` set | Consultation updated, Patient `next_followup_date` set |
| 2 | Patient returns within 3 months | System auto-routes to original doctor if present |
| 3 | Original doctor absent + no override | Error: "Doctor is currently absent" with override option |
| 4 | Override selected | Patient routed to next available doctor of same type |

### FLOW-05: Prescription Partial Dispense → Complete Dispense

| Step | Action | Expected Result |
|------|--------|----------------|
| 1 | Doctor prescribes 3 items | Prescription `pending` with 3 items |
| 2 | Pharmacist dispenses 2 items fully, 1 partially | Prescription → `partially_dispensed` |
| 3 | Pharmacist dispenses remaining quantity of item 3 | Prescription → `dispensed` |
| 4 | Verify cumulative `dispensed_quantity` | Each item: dispensed_quantity = quantity |

### FLOW-06: Consultation Cancellation Cascade

| Step | Action | Expected Result |
|------|--------|----------------|
| 1 | Create consultation with ancillary request and prescription | All records in active states |
| 2 | Doctor cancels consultation with reason | Consultation → `cancelled`, Queue → `Cancelled`, AncillaryRequest → `Cancelled`, Prescription → `cancelled`, Appointment → `cancelled`, PreTriage → `cancelled` |
| 3 | Verify audit log | AuditLog records cancellation with reason |

### FLOW-07: Lab Result Amendment

| Step | Action | Expected Result |
|------|--------|----------------|
| 1 | Lab completes request with results | AncillaryRequest → `Done` with `result_data` |
| 2 | Lab amends result with reason | `previous_result_data` preserves original, `result_data` updated, `is_amended=true`, `amendment_reason` stored |
| 3 | Verify audit trail | AuditLog records amendment with reason |

### FLOW-08: Medicine Lifecycle (Add → Dispense → Deplete → Dispose)

| Step | Action | Expected Result |
|------|--------|----------------|
| 1 | Pharmacist creates medicine with initial batch (100 units) | Medicine + Batch (qty=100, status=active) + InventoryLog |
| 2 | Pharmacist adds second batch (50 units) | New Batch (qty=50) + InventoryLog |
| 3 | Prescription dispenses 90 units from batch 1 (FEFO) | Batch 1: qty=10. InventoryLog created. |
| 4 | Prescription dispenses 15 units (spans batches) | Batch 1: qty=0, status=depleted. Batch 2: qty=45. |
| 5 | Pharmacist disposes batch 2 | Batch 2: qty=0, status=disposed, disposal fields set |
| 6 | Medicine shows "Out of Stock" | Dashboard banner: out of stock count includes this medicine |

---

## EDGE Tests — Boundary and Edge Cases

### EDGE-01: Pediatric Walk-in Cap

| Test | Input | Expected |
|------|-------|----------|
| 6th walk-in pedia (non-emergency) | `classification=Pediatric, appointment_id=null` | Error: "Walk-in slots for Pediatrics are full" |
| 6th walk-in pedia (emergency override) | `classification=Pediatric, is_emergency=true` | Success: `PED-E-001` queue number |
| 6th walk-in pedia after one cancellation | Cancel 1st, then 6th attempt | Success: slot freed by cancellation |

### EDGE-02: Appointment Date Validation

| Test | Input | Expected |
|------|-------|----------|
| Book for today | `preferred_date = today` | Error: "after:today" validation fails |
| Book for yesterday | `preferred_date = yesterday` | Error: validation fails |
| Check in before appointment date | Check-in on Mon for Tue appointment | Error: "cannot be checked in before the scheduled appointment date" |
| Check in on appointment date | Check-in on correct date | Success |

### EDGE-03: Vitals Boundary Values

| Test | Input | Expected |
|------|-------|----------|
| BP = 260/160 | blood_pressure = "260/160" | Success (at boundary) |
| BP = 261/161 | blood_pressure = "261/161" | Error: "physically unlikely" |
| BP systolic < diastolic | blood_pressure = "80/120" | Error: "Systolic must be higher" |
| Temperature = 34.0 | temperature = 34.0 | Success (at boundary) |
| Temperature = 33.9 | temperature = 33.9 | Error: out of range |
| Weight = 0.5 | weight = 0.5 | Success (at boundary, premature infant) |
| SpO2 = 50 | oxygen_saturation = 50 | Success (critical but valid) |
| SpO2 = 49 | oxygen_saturation = 49 | Error: out of range |

### EDGE-04: Prescription Expiry

| Test | Input | Expected |
|------|-------|----------|
| Dispense expired prescription | Prescription with `expires_at` in the past | Error: "expired" message |
| Auto-expire stale prescription | Run `prescriptions:expire-stale` with pending prescription past expiry | Prescription → `expired` |
| Dispense on expiry day | Prescription expires today, attempt dispense | Depends on time-of-day comparison |

### EDGE-05: Stock Deduction Edge Cases

| Test | Input | Expected |
|------|-------|----------|
| Dispense more than available stock | Request 100, stock = 50 | Error: "only 50 in stock" |
| Dispense exactly available stock | Request 50, stock = 50 | Success, batch → depleted |
| Dispense 0 for all items | All items quantity = 0 | Error: "Enter a quantity greater than zero" |
| Dispense more than outstanding | Request 10, outstanding = 5 | Error: "only 5 still outstanding" |
| FEFO order verification | 2 batches: A (exp Dec), B (exp Nov) | B deducted first (earlier expiry) |

### EDGE-06: Duplicate Patient Prevention

| Test | Input | Expected |
|------|-------|----------|
| Same name + DOB as existing patient | first_name=John, last_name=Doe, dob=2000-01-01 (exists) | Error: "already exists in the system" |
| Same patient in triage queue today | Existing patient already `waiting` in PreTriage | Error: "already waiting in the queue" |
| Same patient with active consultation | Patient has `active` consultation today | Error: "already queued or being processed" |

### EDGE-07: File Upload Attacks

| Test | Input | Expected |
|------|-------|----------|
| Valid JPEG | Standard .jpg file | ✅ Accepted |
| Valid PNG | Standard .png file | ✅ Accepted |
| Valid WebP | Standard .webp file | ✅ Accepted |
| GIF file | .gif image | ❌ Rejected: "Only JPEG, JPG, PNG, and WebP" |
| SVG file (XSS vector) | .svg file | ❌ Rejected |
| PHP file renamed to .jpg | shell.php → shell.jpg | ❌ Rejected: magic bytes don't match JPEG |
| Double extension | image.png.exe | ❌ Rejected: double extension detection |
| No extension | file (no extension) | ❌ Rejected |
| BMP file | .bmp image | ❌ Rejected |
| HEIC file | .heic photo | ❌ Rejected |
| Corrupted JPEG (wrong magic bytes) | .jpg with PNG content | ❌ Rejected: MIME-extension mismatch |

### EDGE-08: OTP Verification Edge Cases

| Test | Input | Expected |
|------|-------|----------|
| Wrong OTP 5 times | 5 incorrect attempts | Lockout for 3 minutes (tier 1) |
| Wrong OTP after 1st lockout | Attempt during lockout | Extended lockout (5 minutes, tier 2) |
| Expired OTP | Enter OTP after 10-minute window | Error: OTP expired |
| Reuse OTP | Submit same OTP twice | Error: already verified / session exists |

---

## ACCESS Tests — Role-Based Access Control

### ACCESS-01: Route Access Matrix Verification

For each role, verify access to every route group:

| Test ID | Role | Route | Expected |
|---------|------|-------|----------|
| ACC-01a | `vitals_nurse` | GET `/admin/dashboard` | Redirect to triage dashboard with warning |
| ACC-01b | `information_desk` | GET `/doctor/dashboard` | Redirect to frontdesk with warning |
| ACC-01c | `pharmacy` | GET `/lab/dashboard` | Redirect to pharmacy dashboard |
| ACC-01d | `laboratory` | POST `/pharmacy/dispense/{id}` | Redirect with warning |
| ACC-01e | `super_admin` | GET `/frontdesk/registration` | ❌ **Expected: Access. Actual: Redirect** (Bug A-01) |
| ACC-01f | `super_admin` | GET `/triage/dashboard` | ❌ **Expected: Access. Actual: Redirect** (Bug A-02) |
| ACC-01g | Unauthenticated | GET `/admin/dashboard` | 404 |
| ACC-01h | Unauthenticated | GET `/doctor/dashboard` | 404 |

### ACCESS-02: Gate Authorization

| Test ID | User Role | Gate | Expected |
|---------|----------|------|----------|
| ACC-02a | `super_admin` | `view-audit-logs` | ✅ Allowed |
| ACC-02b | `admin` | `view-audit-logs` | ❌ Denied |
| ACC-02c | `super_admin` | `force-delete` | ✅ Allowed |
| ACC-02d | `admin` | `force-delete` | ❌ Denied |
| ACC-02e | `admin` | `manage-content` | ✅ Allowed |
| ACC-02f | `pharmacy` | `manage-content` | ❌ Denied |
| ACC-02g | `super_admin` | `delete-staff` | ✅ Allowed |
| ACC-02h | `admin` | `delete-staff` | ❌ Denied |

### ACCESS-03: Cross-Role Action Attempts

| Test ID | Actor | Action | Expected |
|---------|-------|--------|----------|
| ACC-03a | Doctor A | Start consultation assigned to Doctor B | Verify ownership check → 403 |
| ACC-03b | Doctor A | Cancel prescription created by Doctor B | 403: "You can only cancel prescriptions you created" |
| ACC-03c | `laboratory` user | Process `Radiology` type request | Currently succeeds (Bug A-05) — should be 403 |
| ACC-03d | `information_desk` | Access `/pharmacy/medicines` | Redirect with warning |
| ACC-03e | Any auth user | GET `/api/medicines/search?q=para` | ✅ Currently succeeds (Bug L-08) — should be role-restricted |

### ACCESS-04: Public Endpoint Security

| Test ID | Action | Expected |
|---------|--------|----------|
| ACC-04a | Book appointment without OTP | Should fail — OTP verification required |
| ACC-04b | Access appointment dashboard without session | Redirect to manage page |
| ACC-04c | Cancel appointment with different reference # in session | Should fail — session mismatch |
| ACC-04d | Submit 51 bookings from same IP | 50th succeeds, 51st rate-limited |
| ACC-04e | Send 4 OTPs in 1 hour | 3rd succeeds, 4th rate-limited |

---

## DATA Tests — Data Integrity

### DATA-01: Patient Registration Validation

| Test ID | Field | Input | Expected |
|---------|-------|-------|----------|
| DAT-01a | first_name | `<script>alert(1)</script>` | ❌ Rejected: regex fails (no angle brackets) |
| DAT-01b | first_name | `María José` | ⚠️ Check: accented characters not in regex `[A-Za-z\s.\-ñÑ]` |
| DAT-01c | philhealth_number | `12-1234567890-1` (14 digits) | ❌ Rejected: regex expects `\d{2}-\d{9}-\d{1}` |
| DAT-01d | philhealth_number | `12-123456789-1` | ✅ Accepted |
| DAT-01e | contact_number | `+639123456789` | ❌ Rejected: regex expects `09\d{9}` |
| DAT-01f | dob | `2030-01-01` | ❌ Rejected: `before:today` |
| DAT-01g | classification = Senior, age = 55 | classification=Senior Citizen, dob makes age 55 | Error: "at least 60 years old" |
| DAT-01h | classification = Pediatric, age = 15 | classification=Pediatric, dob makes age 15 | Error: "restricted to patients 12 years old and below" |

### DATA-02: Audit Trail Immutability

| Test ID | Action | Expected |
|---------|--------|----------|
| DAT-02a | Attempt `AuditLog::find(1)->update(['action' => 'tampered'])` | Model event returns `false`, update blocked |
| DAT-02b | Attempt `AuditLog::find(1)->delete()` | Model event returns `false`, delete blocked |
| DAT-02c | Verify `DB::table('audit_logs')->where('id', 1)->update(...)` | ⚠️ Bypasses model — succeeds (known limitation) |

### DATA-03: Inventory Log Completeness

| Test ID | Action | Expected |
|---------|--------|----------|
| DAT-03a | Dispense medicine | InventoryLog with action=Dispensed, negative qty, batch reference, patient info |
| DAT-03b | Add stock | InventoryLog with action=Added, positive qty, batch reference |
| DAT-03c | Dispose batch | InventoryLog with action=Deducted, negative qty, disposal reason |
| DAT-03d | Adjust stock up | InventoryLog with action=Added, positive delta |
| DAT-03e | Adjust stock down | InventoryLog with action=Deducted, negative delta |

---

## CONCUR Tests — Concurrent Access

### CONCUR-01: Simultaneous Dispensing

| Test | Setup | Expected |
|------|-------|----------|
| Two pharmacists dispense same prescription | Prescription with 10 units, both submit dispense(5) simultaneously | One succeeds, other gets "stock changed" or "outstanding quantity" error. Total dispensed = 5, not 10. |

### CONCUR-02: Simultaneous Appointment Booking

| Test | Setup | Expected |
|------|-------|----------|
| Two patients book last available slot | Both pass availability check, both submit | ⚠️ Both may succeed (no lock). Verify slot count after. |

### CONCUR-03: Simultaneous Stock Adjustment

| Test | Setup | Expected |
|------|-------|----------|
| Two staff adjust same batch | Batch qty=100, Staff A sets 90, Staff B sets 80 | ⚠️ Last write wins. Expected: 80 or 90. Potential data loss. |

---

## REGRESS Tests — Loophole-Specific Regression

| Test ID | Loophole | Test | Expected After Fix |
|---------|---------|------|-------------------|
| REG-01 | L-01 | Book appointment, verify status is `approved` (not `pending`) | After R-01 fix |
| REG-02 | L-02 | POST appointment for non-Main-Health-Center facility | 422 error after R-02 fix |
| REG-03 | L-05 | Run `batches:expire`, verify expired batches → `expired` status | After R-03 fix |
| REG-04 | L-07 | Restore disposed batch (super_admin) | Batch → active, qty restored, audit log created. After R-04 fix |
| REG-05 | L-10 | Cancel partially-dispensed Rx with "return stock" | Stock returned to batches, InventoryLog created. After R-07 fix |
| REG-06 | A-01 | Super admin accesses /frontdesk/registration | 200 OK after R-05 fix |
| REG-07 | A-05 | Lab user processes Radiology request | 403 after R-06 fix |
| REG-08 | L-20 | Register patient with duplicate PhilHealth # | Error: "PhilHealth number already registered" after R-10 fix |
| REG-09 | L-21 | Add batch with duplicate batch_number for same medicine | Error: "Batch number already exists" after R-11 fix |

---

## Test Environment Requirements

| Requirement | Details |
|-------------|---------|
| **Database** | Seeded with: 2+ doctors (regular + pedia), 1+ nurse, 1+ vitals nurse, 1+ receptionist, 1+ lab tech, 1+ pharmacist, 1+ admin, 1 super_admin |
| **Medicines** | 3+ medicines with multiple batches (including near-expiry and expired) |
| **Patients** | 5+ patients across classifications (Regular Adult, Senior, PWD, Pediatric) |
| **Appointments** | Mix of pending, approved, arrived, cancelled |
| **Consultations** | At least 1 in each status: queued, active, completed, cancelled |
| **Mail** | Use `log` driver or Mailtrap for email verification |
| **Scheduler** | Test scheduled commands via `php artisan schedule:test` |

## Existing Test Coverage

The project has existing feature tests in `tests/Feature/`:
- `AdminStaffAndProfileSettingsTest.php`
- `NurseTriageQueueRedesignTest.php`
- `StaffIdGenerationTest.php`

These should be reviewed for alignment with the test plan above and extended to cover the identified gaps.
