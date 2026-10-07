# Phase 5: Data Integrity & Safety Audit

---

## Validation Coverage Analysis

### Patient Registration (`RegistrationController::storePatient` / `updatePatient`)

| Field | Validation Rule | Completeness | Notes |
|-------|----------------|-------------|-------|
| `first_name` | `required\|string\|max:255\|regex:/^[A-Za-z\s.\-ñÑ]+$/` | ✅ Strong | Alpha + spaces + dots + hyphens + ñ |
| `last_name` | Same as first_name | ✅ Strong | |
| `middle_name` | `nullable\|string\|max:255\|regex:...` | ✅ | |
| `suffix` | `nullable\|string\|max:20\|regex:...` | ✅ | |
| `sex` | `required\|in:Male,Female` | ✅ | |
| `dob` | `required\|date\|before:today` | ✅ | Prevents future dates |
| `blood_type` | `required\|string\|max:10` | ⚠️ | No `in:` constraint — accepts any string up to 10 chars |
| `philhealth_number` | `regex:/^\d{2}-\d{9}-\d{1}$/` | ✅ Strong | Exact format enforcement |
| `contact_number` | `regex:/^09\d{9}$/` | ✅ Strong | Philippine mobile format |
| `email` | `nullable\|email:rfc,dns\|max:255` | ✅ Strong | RFC + DNS validation |
| `address` | `required\|string` | ⚠️ | No max length |
| `classification` | `required\|in:Regular Adult,Senior Citizen,PWD,Pediatric` | ✅ | |
| `guardian_*` | Conditional `required_if:classification,Pediatric` | ✅ | Guardian fields required for pediatric |
| **Age-Classification cross-validation** | Manual code check | ✅ | Pediatric ≤12, Senior ≥60, validated in controller |
| **Duplicate check** | `first_name + last_name + dob` | ⚠️ | Basic — no soundex or fuzzy matching |

### Appointment Booking (`PublicController::storeAppointment`)

| Field | Validation | Completeness | Notes |
|-------|-----------|-------------|-------|
| `first_name` / `last_name` | `required\|string\|max:255\|regex:...` | ✅ | |
| `dob` | `required\|date\|before:today` | ✅ | |
| `email` | `required\|email:rfc,dns\|max:255` | ✅ | Required for OTP |
| `preferred_date` | `required\|date\|after:today` | ✅ | Future dates only |
| `preferred_time` | `required\|string` | ⚠️ | No format validation on time string |
| `type` | `required\|in:pedia,general` | ✅ | |
| `contact_number` | `required\|regex:/^09\d{9}$/` | ✅ | |
| **Duplicate check** | Name + DOB + date combination | ✅ | Via `checkDuplicate` AJAX |
| **Rate limiting** | 50/day/IP | ✅ | Via `throttle:booking` middleware |
| **OTP verification** | Required, with backoff | ✅ Strong | Exponential backoff tiers |
| **Data privacy consent** | `required\|accepted` | ✅ | `data_privacy_agreed` checkbox |

### Vitals Recording (`TriageController::store`)

| Field | Validation | Completeness | Notes |
|-------|-----------|-------------|-------|
| `blood_pressure` | `regex:/^\d{2,3}\/\d{2,3}$/` + custom range check | ✅ Strong | Systolic 60-260, Diastolic 30-160, Sys > Dia |
| `temperature` | `numeric\|between:34.0,43.0` | ✅ | Celsius range |
| `weight` | `numeric\|between:0.5,400` | ✅ | kg range |
| `height` | `numeric\|between:30,250` | ✅ | cm range |
| `heart_rate` | `integer\|between:30,220` | ✅ | bpm range |
| `respiratory_rate` | `integer\|between:8,80` | ✅ | breaths/min |
| `pulse_rate` | `integer\|between:30,220` | ✅ | bpm range |
| `oxygen_saturation` | `integer\|between:50,100` | ✅ | percentage |
| `symptoms` | `required\|string` | ⚠️ | No max length |
| `classification` | `required\|in:Adult,Senior,Pediatric,PWD` | ⚠️ | Different values than patient (`Adult` vs `Regular Adult`) — potential mismatch |

### Medicine / Inventory (`PharmacyController::storeMedicine` / `addStock`)

| Field | Validation | Completeness | Notes |
|-------|-----------|-------------|-------|
| `name` | `required\|string\|max:255` | ✅ | Duplicate check by name |
| `generic_name` | `nullable\|string\|max:255` | ✅ | |
| `batch_number` | `required\|string\|max:40` | ⚠️ | Normalized but not unique per medicine |
| `expiration_date` | `required\|date\|after:today` | ✅ | Future dates only |
| `quantity` | `required\|integer\|min:1` | ✅ | |
| `disposal_reason` | `required\|in:Expired,Damaged...` | ✅ | Enum-constrained |
| `adjustment_reason` | `required\|in:Physical Recount...` | ✅ | Enum-constrained |

### File Upload (`SecureImage` rule)

| Check | Implementation | Completeness |
|-------|---------------|-------------|
| Extension whitelist | `jpg, jpeg, png, webp` | ✅ |
| Double extension rejection | `str_contains($basename, '.')` | ✅ |
| No-extension rejection | Empty extension check | ✅ |
| MIME type validation | PHP `finfo` server-side detection | ✅ |
| Extension-MIME consistency | `match` expression cross-check | ✅ |
| Magic bytes validation | First 12 bytes signature check | ✅ |
| WebP full validation | RIFF header + WEBP at offset 8 | ✅ |
| Max file size | `max:10240` (10MB) | ✅ |
| GIF rejection | ✅ Blocked | Not in allowed list |
| SVG rejection | ✅ Blocked | Not in allowed list (XSS vector) |
| PDF rejection | ✅ Blocked | Not in allowed list |

> **Positive finding**: The `SecureImage` rule is exceptionally well-implemented with defense-in-depth (3-layer validation).

---

## Soft Delete vs Hard Delete Analysis

| Model | Soft Delete? | Hard Delete Available? | Guard | Recommendation |
|-------|-------------|----------------------|-------|----------------|
| `Patient` | ✅ `SoftDeletes` | Yes — `force-delete` Gate (super_admin) | ✅ Correct | Archive first, force-delete only by super_admin |
| `User` (Staff) | ✅ `SoftDeletes` | Yes — `delete-staff` Gate (super_admin) | ✅ Correct | |
| `Announcement` | ✅ `SoftDeletes` | Yes — `delete-announcements` Gate (super_admin) | ✅ Correct | |
| `Consultation` | ❌ No soft delete | No delete endpoint | ⚠️ Acceptable | Consultations are cancelled, not deleted |
| `Prescription` | ❌ No soft delete | No delete endpoint | ⚠️ Acceptable | Prescriptions are cancelled/expired, not deleted |
| `AncillaryRequest` | ❌ No soft delete | No delete endpoint | ⚠️ Acceptable | Requests are cancelled/rejected, not deleted |
| `MedicineBatch` | ❌ No soft delete | No delete endpoint | ⚠️ Acceptable | Batches are disposed, not deleted |
| `Medicine` | ❌ No soft delete | No delete endpoint | ⚠️ | Medicines are archived (`is_active=false`), functionally soft delete |
| `PreTriage` | ❌ No soft delete | `cleanup-vitals` command | ⚠️ | Orphaned records are cleaned daily |
| `AuditLog` | ❌ Immutable | Model blocks update/delete | ✅ Correct | Audit logs should never be deleted |
| `InventoryLog` | ❌ No soft delete | No delete endpoint | ✅ Correct | Inventory logs should never be deleted |
| `Appointment` | ❌ No soft delete | Prunable (30d cancelled) | ⚠️ | Cancelled appointments pruned after 30 days |

> **Recommendation**: For records tied to patient care, dispensing, or audit — the system correctly uses archive/deactivate over permanent deletion. The prunable trait on appointments should be extended to retain cancelled appointments for at least 1 year for regulatory compliance.

---

## Cascading Operations Audit

### Consultation Cancellation Cascade

When `DoctorController::cancelConsultation` is called:

| Step | Action | Evidence |
|------|--------|----------|
| 1 | Consultation → `cancelled` | DoctorController L679 |
| 2 | Queue → `Cancelled` | DoctorController L695 |
| 3 | Active ancillary requests → `Cancelled` | DoctorController L705 |
| 4 | Pending prescription → `cancelled` | DoctorController L718 |
| 5 | Appointment → `cancelled` (if linked) | DoctorController L729 |
| 6 | PreTriage → `cancelled` | DoctorController L735 |
| 7 | AuditLog recorded | DoctorController L741 |
| 8 | QueueUpdated broadcast | DoctorController L747 |

> **Positive finding**: Cancellation cascade is comprehensive. All related records are properly transitioned.

### Patient Deletion Cascade

When admin soft-deletes a patient:

| Related Record | Cascade Action | Issue |
|---------------|---------------|-------|
| `consultations` | ❌ Not cascaded | Consultations reference `patient_id` (string FK). Orphaned consultations remain queryable. |
| `prescriptions` | ❌ Not cascaded | Same issue |
| `appointments` | ❌ Not cascaded | Same issue — uses name matching, not FK |
| `medical_cases` | ❌ Not cascaded | Orphaned case records |
| `pre_triages` | ❌ Not cascaded | References `patient_id` but may be null for new patients |

> **Finding**: Patient soft-delete does NOT cascade to related records. This is partially intentional (clinical records should be retained for audit), but it means orphaned records exist after deletion. The `patient_id` foreign key in consultations is a string reference, not a true FK constraint.

---

## Data Encryption at Rest

| Field | Encryption | Evidence |
|-------|-----------|---------|
| `patients.philhealth_number` | ✅ Laravel `encrypted` cast | Patient.php L34 |
| `patients.guardian_philhealth` | ✅ Laravel `encrypted` cast | Patient.php L35 |
| `users.password` | ✅ bcrypt hash | Laravel default |
| All other PII fields | ❌ Plaintext | Names, DOB, contact numbers, addresses stored in plaintext |
| `audit_logs.changes` | ❌ Plaintext JSON | Contains old/new values including PII changes |
| `consultations.diagnosis` | ❌ Plaintext | Medical diagnosis in plain text |
| `ancillary_requests.result_data` | ❌ Plaintext JSON | Lab results in plain text |

> **Finding**: Only PhilHealth numbers are encrypted. Other PII (names, contact numbers, addresses) and PHI (diagnoses, lab results) are stored in plaintext. For Data Privacy Act (RA 10173) compliance, consider encrypting sensitive medical data.

---

## Input Sanitization

| Mechanism | Models Using It | What It Does |
|-----------|----------------|-------------|
| `Sterilizable` trait | `Patient` | Strips HTML tags on save for listed fields |
| Title case normalization | `RegistrationController`, `TriageController` | `ucwords(strtolower(...))` on name fields |
| `normalizeBatchNumber` | `PharmacyController` | Trims, collapses whitespace, uppercases batch numbers |
| `normalizeBarangay` | `Patient` model | Fuzzy-matches barangay from address, normalizes Roman numerals |
| Blade escaping | All views (assumed) | `{{ }}` auto-escapes HTML entities |

> **Gap**: The `Message` model (chat) does NOT use the `Sterilizable` trait. If chat messages are rendered unescaped, XSS is possible (see L-22 in Phase 3).

---

## Transaction Safety

| Operation | Uses DB Transaction? | Uses Locks? | Evidence |
|-----------|---------------------|-------------|---------|
| Dispensing | ✅ | ✅ `lockForUpdate()` | PharmacyController L269 |
| Queue number generation | ✅ | ✅ `lockForUpdate()` | RegistrationController L717-727 |
| Patient ID generation | ✅ | ✅ `lockForUpdate()` | Patient.php L50-58 |
| Staff ID generation | ✅ | ✅ `lockForUpdate()` | User.php |
| Medicine creation + batch | ✅ | ❌ | PharmacyController L808 |
| Stock addition | ✅ | ❌ | PharmacyController L869 |
| Batch disposal | ✅ | ❌ | PharmacyController L942 |
| Stock adjustment | ✅ | ❌ | PharmacyController L1019 |
| Consultation creation | ❌ | ❌ | RegistrationController L730 (outside txn but queue# is inside) |
| Prescription creation | ❌ | ❌ | InteractsWithPrescriptions L118-137 |
| Lab result completion | ❌ | ❌ | LabController L220 |

> **Finding**: Critical financial/sequential operations (dispensing, ID generation, queue numbers) correctly use transactions with locks. Non-critical operations use transactions without locks, which is acceptable. Some operations (consultation creation, prescription creation) do NOT use transactions at all — a failure partway through could leave inconsistent state.

---

## Concurrent Access Scenarios

| Scenario | Protected? | Mechanism |
|----------|-----------|-----------|
| Two pharmacists dispense same prescription | ✅ | `lockForUpdate()` on items and batches |
| Two nurses queue same patient | ✅ | Duplicate check in `storeVisit` L539-546 |
| Two doctors start same consultation | ⚠️ Partial | Status check (`queued` only), but no lock. Race possible. |
| Two bookings for last slot | ❌ | No lock between availability check and insert |
| Two pharmacists dispose same batch | ✅ | Status check (`disposed` check at L934) inside transaction |
| Two staff adjusting same batch stock | ⚠️ Partial | No lock. Both could read old qty, compute new qty, overwrite. |
