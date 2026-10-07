# Phase 1: End-to-End Process Flows

---

## Flow A: Appointment to Consultation Closing

### A.1 Numbered Walkthrough

| Step | Actor | Screen/Page | Endpoint/Function | DB Records | Status Before → After | Validation | Trigger for Next Step | Completeness |
|------|-------|------------|-------------------|------------|----------------------|------------|----------------------|-------------|
| 1 | Patient | `/appointment` (public) | `PublicController::createAppointment` | — | — | — | Page load | Complete |
| 2 | Patient | Appointment form | `PublicController::checkAvailability` | — | — | Date validation, slot availability | AJAX check | Complete |
| 3 | Patient | Appointment form | `PublicController::getTimeSlots` | — | — | — | Time slot selection | Complete |
| 4 | Patient | Appointment form | `PublicController::checkDuplicate` | — | — | Duplicate check (name+dob+date) | AJAX pre-submit | Complete |
| 5 | Patient | Appointment form | `PublicController::sendOtp` | — | — | Email required, rate limit (3/hr) | OTP email sent | Complete |
| 6 | Patient | OTP verification | `PublicController::verifyOtp` | — | — | OTP match, expiry, max attempts with backoff | OTP verified | Complete |
| 7 | Patient | Appointment form submit | `PublicController::storeAppointment` | Appointment created | — → `pending` | Full validation, throttle:booking (50/day/IP), data privacy agreed | Appointment created + confirmation email | Complete |
| 8 | Patient | `/appointment/manage` | `PublicController::manageAppointment` | — | — | Reference # + email auth (session) | Login to management | Complete |
| 9 | Patient | `/appointment/dashboard` | `PublicController::dashboardAppointment` | — | — | Session-based auth | View status | Complete |
| 10 | Patient | Dashboard | `PublicController::cancelAppointment` | Appointment updated | `pending`/`approved`/`rescheduled` → `cancelled` | Status check | Cancellation email sent | Complete |
| 11 | Patient | Dashboard | `PublicController::rescheduleAppointment` | Appointment updated | `pending`/`approved`/`rescheduled` → `rescheduled` | New date validation | Reschedule email sent | Complete |
| 12 | System | — | `SendAppointmentReminders` command | — | — | Day-before check | Email reminder at 08:00 | Complete |
| 13 | Receptionist | Front desk dashboard | `RegistrationController::checkIn` | Appointment updated | `pending`/`approved`/`rescheduled` → `arrived` | Date check (no early check-in) | Patient proceeds to triage | Complete |
| 14 | Receptionist | Front desk dashboard | `RegistrationController::markNoShow` | Appointment updated | `approved`/`rescheduled`/`pending` → `no_show` | Status check | Record updated | Complete |
| 15 | Receptionist | Front desk dashboard | `RegistrationController::cancelAppointment` | Appointment updated | `pending`/`approved`/`rescheduled` → `cancelled` | Reason required (nullable) | Cancellation email sent | Complete |
| 16 | Vitals Nurse | Triage dashboard | `TriageController::store` | PreTriage created | — → `waiting` | Vitals validation (BP, temp, etc.), duplicate check | Appointment → `triaged` | Complete |
| 17 | Receptionist | Registration index | `RegistrationController::storeVisit` | Consultation + Queue created | PreTriage `waiting` → `claimed`; Appointment → `registered` | Pre-triage required, severity, duplicate queue check | Patient queued | Complete |
| 18 | Doctor | Doctor dashboard | `DoctorController::startConsultation` | Consultation updated | `queued` → `active` | Ownership check, Queue → `Calling` | Doctor opens consultation | Complete |
| 19 | Doctor | Consultation page | `DoctorController::storeAncillaryRequest` | AncillaryRequest created | Consultation `active` → `awaiting_results` | Valid test from catalog, no duplicate active test | Lab/Rad request sent | Complete |
| 20 | Doctor | Consultation page | `DoctorController::completeConsultation` | Consultation + MedicalCase + Prescription | `active` → `completed`; Appointment → `done`; PreTriage → `completed`; Queue → `Completed` | Diagnosis required | Pharmacy notified if RHU Rx | Complete |
| 21 | Doctor | Consultation page | `DoctorController::cancelConsultation` | Consultation + linked records | `active`/`queued` → `cancelled` | Reason required, cascading cancel | All linked records cancelled | Complete |
| 22 | Doctor | Completed consultation | `DoctorController::storeAddendum` | Consultation notes appended | `completed`/`done` (unchanged) | Only attending clinician, max 2000 chars | Audit logged | Complete |

### A.2 Mermaid Flow Diagram

```mermaid
flowchart TD
    A1[Patient: Book Appointment Online] --> A2[OTP Verification]
    A2 --> A3[Appointment Created - Status: pending]
    A3 --> A4{Auto-Approve?}
    A4 -->|No auto-approve found| A4b[Stays pending]
    A4b --> A5
    A3 --> A5{Day of Appointment}
    A5 --> A6[System: Send Reminder Email at 08:00]
    A5 --> A7[Receptionist: Check-In → arrived]
    A5 --> A8[Receptionist: Mark No-Show → no_show]
    A5 --> A9[Receptionist: Cancel → cancelled]
    A5 --> A10[Patient: Cancel Online → cancelled]
    A5 --> A11[Patient: Reschedule → rescheduled]
    A7 --> A12[Vitals Nurse: Record Vitals → PreTriage waiting]
    A12 --> A13[Receptionist: Register + Queue → Consultation queued]
    A13 --> A14[Doctor: Start Consultation → active]
    A14 --> A15{Orders Needed?}
    A15 -->|Lab/Rad| A16[Create Ancillary Request → awaiting_results]
    A16 --> A17[Lab completes → results_ready]
    A17 --> A14
    A15 -->|No| A18[Doctor: Complete Consultation → completed]
    A14 --> A19[Doctor: Cancel/Walkout → cancelled]
    A18 --> A20[MedicalCase Created + Queue Completed]
    A18 --> A21{Prescription?}
    A21 -->|RHU Items| A22[Prescription Created → Pharmacy Notified]
    A21 -->|OTC Only| A20

    style A4b stroke-dasharray: 5 5
    style A4 stroke-dasharray: 5 5
```

### A.3 Status Lifecycle Tables

#### Appointment Statuses

| Status | Set By | Transitions To | Trigger | Evidence |
|--------|--------|---------------|---------|----------|
| `pending` | System | `approved`¹, `rescheduled`, `cancelled`, `arrived`, `no_show` | Booking submitted | PublicController::storeAppointment L29 |
| `approved` | System¹ | `arrived`, `cancelled`, `rescheduled`, `no_show`, `triaged` | ¹No explicit approve endpoint found | — |
| `rescheduled` | Patient/Staff | `arrived`, `cancelled`, `no_show` | Patient reschedules | PublicController::rescheduleAppointment |
| `arrived` | Receptionist | `triaged`, `registered`, `cancelled` | Check-in at desk | RegistrationController::checkIn L128 |
| `triaged` | Vitals Nurse | `registered` | Vitals recorded | TriageController::store L285 |
| `registered` | Receptionist | `done`, `cancelled` | Patient queued | RegistrationController::storeVisit L764 |
| `done` | Doctor | — (terminal) | Consultation completed | DoctorController::completeConsultation L305 |
| `cancelled` | Multiple | — (terminal) | Cancellation | Multiple controllers |
| `no_show` | Receptionist/System | — (terminal) | Patient didn't arrive | RegistrationController::markNoShow L177 |

> ¹ **Finding**: No explicit "approve" endpoint exists. The `approved` status is in the enum but there is no controller action that transitions `pending` → `approved`. The `FixStuckAppointments` command and front-desk views reference it, suggesting it may have been intended but the auto-approve or manual approve step is **missing**.

#### Consultation Statuses

| Status | Set By | Transitions To | Evidence |
|--------|--------|---------------|----------|
| `queued` | Receptionist | `active` | RegistrationController::storeVisit L737 |
| `active` | Doctor/Nurse | `completed`, `awaiting_results`, `cancelled` | DoctorController::startConsultation L176 |
| `awaiting_results` | Doctor/System | `results_ready`, `active` | DoctorController::storeAncillaryRequest L511 |
| `results_ready` | Lab system | `active` | LabController::syncConsultationReadiness L459 |
| `completed` | Doctor/Nurse | `done`² | DoctorController::completeConsultation L284 |
| `done` | —² | — (terminal) | Referenced in queries but no transition code found |
| `cancelled` | Doctor/Nurse | — (terminal) | DoctorController::cancelConsultation L679 |

> ² **Finding**: `done` is used in `whereIn` clauses alongside `completed` but no code transitions a consultation from `completed` to `done`. These appear to be treated as equivalent terminal states. **Dead status concern**: `done` may be a legacy status with no active setter.

### A.4 End State

The defined end states for Flow A are:
- **Appointment**: `done` (success), `cancelled`, or `no_show`
- **Consultation**: `completed` (success) or `cancelled`
- **PreTriage**: `completed` or `cancelled`
- **Queue**: `Completed` or `Cancelled`

---

## Flow B: Walk-in to Consultation Closing

### B.1 Numbered Walkthrough

| Step | Actor | Screen/Page | Endpoint/Function | DB Records | Status Before → After | Completeness |
|------|-------|------------|-------------------|------------|----------------------|-------------|
| 1 | Patient | Arrives at clinic | — | — | — | N/A |
| 2 | Vitals Nurse | Triage dashboard | `TriageController::store` | PreTriage created | — → `waiting` | Complete |
| 3 | Vitals Nurse | Search patient | `TriageController::searchPatient` | — | — | Complete |
| 4a | Receptionist (existing patient) | Registration index | `RegistrationController::storeVisit` | Consultation + Queue | PreTriage → `claimed` | Complete |
| 4b | Receptionist (new patient) | Registration form | `RegistrationController::registerAndQueue` | Patient + Consultation + Queue | PreTriage → `claimed` | Complete |
| 5+ | Same as Flow A steps 18-22 | — | — | — | — | Complete |

### B.2 Differences from Flow A

| Aspect | Appointment (Flow A) | Walk-in (Flow B) |
|--------|---------------------|------------------|
| Entry point | Online booking, check-in at desk | Direct arrival at vitals station |
| Patient identification | Pre-filled from appointment data | Manual search or new registration |
| Queue number prefix | `APED-` (pedia appointment) | `PED-` / `REG-` / `PRI-` |
| Priority | Appointment priority in pedia interleaving | Classification-based (Senior/PWD get 2:1 ratio) |
| Merge point | Step 16 (Triage) — both flows converge at vitals recording | — |
| Pedia walk-in cap | N/A | 5 walk-in slots/day (configurable), emergency override available |

### B.3 Mermaid Diagram

```mermaid
flowchart TD
    B1[Patient Arrives at Clinic] --> B2[Vitals Nurse: Record Vitals]
    B2 --> B3{Existing Patient?}
    B3 -->|Yes| B4[Nurse searches, links patient_id]
    B3 -->|No| B5[New patient - no patient_id in PreTriage]
    B4 --> B6[Receptionist: storeVisit - Queue + Consultation]
    B5 --> B7[Receptionist: registerAndQueue - Patient + Queue + Consultation]
    B6 --> B8[MERGE: Doctor Dashboard Queue]
    B7 --> B8
    B8 --> B9[Doctor: Start Consultation → active]
    B9 --> B10[Doctor: Complete → completed]
```

---

## Flow C: Laboratory Request Branch

### C.1 Numbered Walkthrough

| Step | Actor | Endpoint/Function | DB Records | Status Before → After | Completeness |
|------|-------|-------------------|------------|----------------------|-------------|
| 1 | Doctor | `DoctorController::storeAncillaryRequest` | AncillaryRequest created | — → `Pending`; Consultation → `awaiting_results` | Complete |
| 2 | Lab Staff | `LabController::collectSpecimen` | AncillaryRequest updated | `Pending` → `Specimen Collected` | Complete |
| 3 | Lab Staff | `LabController::startProcessing` | AncillaryRequest updated | `Pending`/`Specimen Collected` → `In Progress` | Complete |
| 4 | Lab Staff | `LabController::completeRequest` | AncillaryRequest updated | `In Progress` → `Done` | Complete |
| 5 | Lab Staff | — | Consultation synced | Consultation → `results_ready` (if all tests done) | Complete |
| 6 | Doctor | `DoctorController::startConsultation` (resume) | Consultation updated | `results_ready` → `active` | Complete |
| 7 | Lab Staff | `LabController::amendResult` | AncillaryRequest updated (previous data preserved) | `Done` → `Done` (is_amended=true) | Complete |
| 8 | Doctor | `DoctorController::repeatAncillaryRequest` | New AncillaryRequest (parent_id linked) | — → `Pending` (repeat); Consultation → `awaiting_results` | Complete |
| 9 | Lab Staff | `LabController::rejectRequest` | AncillaryRequest updated | `Pending`/`Specimen Collected`/`In Progress` → `Rejected` | Complete |
| 10 | Doctor | `DoctorController::cancelAncillaryRequest` | AncillaryRequest updated | Not `Done` → `Cancelled` | Complete |
| 11 | Lab Staff | `LabController::cancelRequest` | AncillaryRequest updated | Not `Done` → `Cancelled` | Complete |
| 12 | Lab Staff | `LabController::archiveRequest` | AncillaryRequest (archived_at set) | Any → archived (status unchanged) | Complete |
| 13 | Lab Staff | `LabController::restoreRequest` | AncillaryRequest (archived_at cleared) | Archived → `Pending` | Complete |

### C.2 Critical Value Alert Path

When a lab result contains panic/critical values:
1. Lab tech marks `is_critical` and provides `critical_remarks`
2. System flags `_is_critical` in `result_data` JSON
3. `CriticalLabResultNotification` sent to attending doctor/nurse
4. Audit log records "CRITICAL Diagnostic Results Completed"

Evidence: `LabController::completeRequest` L187-234

### C.3 Ancillary Request Status Lifecycle

| Status | Set By | Transitions To | Evidence |
|--------|--------|---------------|----------|
| `Pending` | Doctor | `Specimen Collected`, `In Progress`, `Cancelled`, `Rejected` | DoctorController L507 |
| `Specimen Collected` | Lab Staff | `In Progress`, `Cancelled`, `Rejected` | LabController L124-128 |
| `In Progress` | Lab Staff | `Done`, `Cancelled`, `Rejected` | LabController L156-159 |
| `Done` | Lab Staff | — (terminal, but amendable) | LabController L201-206 |
| `Cancelled` | Doctor/Lab | — (terminal) | Multiple |
| `Rejected` | Lab Staff | — (terminal) | LabController L329-334 |

### C.4 Mermaid Diagram

```mermaid
flowchart TD
    C1[Doctor: Create Lab Request → Pending] --> C2[Lab: Collect Specimen → Specimen Collected]
    C1 --> C3[Lab: Start Processing → In Progress]
    C2 --> C3
    C3 --> C4[Lab: Complete + Results → Done]
    C4 --> C5[System: Sync Consultation → results_ready]
    C5 --> C6[Doctor: Resume Consultation → active]
    C4 --> C7[Lab: Amend Result → Done with amendment]
    C1 --> C8[Doctor/Lab: Cancel → Cancelled]
    C2 --> C8
    C3 --> C8
    C1 --> C9[Lab: Reject → Rejected]
    C2 --> C9
    C3 --> C9
    C4 -.-> C10[Doctor: Repeat Test → New Pending]
    C10 --> C2

    C4 -->|Critical| C11[System: Notify Doctor - CRITICAL ALERT]

    style C10 stroke-dasharray: 5 5
```

---

## Flow D: Pharmacy/Prescription Branch

### D.1 Numbered Walkthrough

| Step | Actor | Endpoint/Function | DB Records | Status Before → After | Completeness |
|------|-------|-------------------|------------|----------------------|-------------|
| 1 | Doctor/Nurse | `completeConsultation` → `InteractsWithPrescriptions::recordPrescription` | Prescription + PrescriptionItems | — → `pending` | Complete |
| 2 | System | `InteractsWithPrescriptions::notifyPharmacy` | Notification sent | — | Complete |
| 3 | Pharmacist | `PharmacyController::dashboard` | — (read) | — | Complete |
| 4 | Pharmacist | `PharmacyController::dispense` | Prescription, PrescriptionItems, MedicineBatches, InventoryLogs | `pending` → `dispensed` or `partially_dispensed` | Complete |
| 5 | Pharmacist | `PharmacyController::cancel` | Prescription updated | `pending`/`partially_dispensed` → `cancelled` | Complete |
| 6 | Doctor | `DoctorController::cancelByPrescriber` | Prescription updated | `pending`/`partially_dispensed` → `cancelled` | Complete |
| 7 | System | `ExpireStalePrescriptions` command | Prescription updated | `pending` → `expired` | Complete |

### D.2 Dispensing Logic (FEFO)

1. Stock pre-flight check: ensures sufficient unexpired stock per item
2. Transaction with row-level locks (`lockForUpdate()`)
3. Batches ordered by `expiration_date ASC` (First-Expiry-First-Out)
4. Each batch decremented, marked `depleted` if qty reaches 0
5. `InventoryLog` created per batch deduction
6. Cumulative `dispensed_quantity` tracked on `PrescriptionItem`
7. Prescription status: `dispensed` if all items fully dispensed, else `partially_dispensed`

Evidence: `PharmacyController::dispense` L268-376

### D.3 Prescription Status Lifecycle

| Status | Set By | Transitions To | Evidence |
|--------|--------|---------------|----------|
| `pending` | Doctor/Nurse | `dispensed`, `partially_dispensed`, `cancelled`, `expired` | InteractsWithPrescriptions L122 |
| `partially_dispensed` | Pharmacist | `dispensed`, `cancelled` | PharmacyController L356 |
| `dispensed` | Pharmacist | — (terminal) | PharmacyController L356 |
| `cancelled` | Pharmacist/Doctor | — (terminal) | PharmacyController L1193, DoctorController L636 |
| `expired` | System | — (terminal) | ExpireStalePrescriptions command |

### D.4 Mermaid Diagram

```mermaid
flowchart TD
    D1[Doctor: Complete Consultation with Rx] --> D2{RHU Items?}
    D2 -->|Yes| D3[Prescription Created → pending]
    D2 -->|No - OTC only| D4[Text saved in consultation.prescription field only]
    D3 --> D5[Pharmacy Notified]
    D5 --> D6[Pharmacist: Review in Dashboard]
    D6 --> D7{Stock Available?}
    D7 -->|Yes| D8[Dispense → FEFO Deduction]
    D7 -->|Partial| D9[Partial Dispense → partially_dispensed]
    D7 -->|No| D10[Cannot dispense - error shown]
    D8 --> D11[Prescription → dispensed]
    D9 --> D12[Remaining stays in queue]
    D12 --> D6
    D6 --> D13[Pharmacist: Cancel → cancelled]
    D3 --> D14[Doctor: Cancel by Prescriber → cancelled]
    D3 --> D15[System: Auto-Expire → expired]

    D3 -.-> D16["Substitution support"]
    style D16 stroke-dasharray: 5 5
    style D4 fill:#fff3cd
```

> **Finding**: No substitution mechanism exists. If a prescribed medicine is out of stock, the pharmacist must ask the doctor to cancel and re-prescribe. There is no in-system substitution workflow.

---

## Flow E: Inventory and Supply Management

### E.1 Numbered Walkthrough

| Step | Actor | Endpoint/Function | DB Records | Completeness |
|------|-------|-------------------|------------|-------------|
| 1 | Pharmacist | `PharmacyController::storeMedicine` | Medicine + MedicineBatch + InventoryLog | Complete |
| 2 | Pharmacist | `PharmacyController::updateMedicine` | Medicine updated (name, generic, form, category, unit) | Complete |
| 3 | Pharmacist | `PharmacyController::addStock` | MedicineBatch + InventoryLog | Complete |
| 4 | Pharmacist | `PharmacyController::toggleMedicineStatus` | Medicine `is_active` toggled | Complete |
| 5 | Pharmacist | `PharmacyController::disposeBatch` | MedicineBatch → `disposed`, qty → 0, InventoryLog | Complete |
| 6 | Pharmacist | `PharmacyController::adjustStock` | MedicineBatch qty adjusted, InventoryLog | Complete |
| 7 | System | Dashboard banners | Expired, expiring-soon, low-stock, out-of-stock counts | Complete |
| 8 | Pharmacist | `PharmacyController::medicines` | Filterable list with search, form filter, status filter | Complete |
| 9 | Pharmacist | `PharmacyController::exportStockCsv` | CSV export | Complete |
| 10 | Pharmacist | `PharmacyController::writtenOff` | Disposed batch history with search, date range, reason filter | Complete |

### E.2 Medicine Batch Status Lifecycle

| Status | Set By | Transitions To | Evidence |
|--------|--------|---------------|----------|
| `active` (default) | System | `depleted`, `disposed` | MedicineBatch default |
| `depleted` | System | `active` (via adjust) | PharmacyController L319 (dispensing), L1020 (adjust) |
| `disposed` | Pharmacist | — (terminal) | PharmacyController L943-944 |

### E.3 Disposal Reasons (Validated)

- Expired
- Damaged / Broken
- Contaminated
- Supplier Recall
- Other (requires notes)

Evidence: `PharmacyController::disposeBatch` L917

### E.4 Stock Adjustment Reasons (Validated)

- Physical Recount
- Damage / Breakage
- Audit Correction
- Received Adjustment
- Other (requires notes)

Evidence: `PharmacyController::adjustStock` L987

### E.5 Mermaid Diagram

```mermaid
flowchart TD
    E1[Pharmacist: Register New Medicine] --> E2[Medicine + Initial Batch Created]
    E2 --> E3[Pharmacist: Add Stock - New Batch]
    E2 --> E4[Pharmacist: Update Medicine Info]
    E2 --> E5[Pharmacist: Toggle Active/Archived]
    E2 --> E6[Batch: Dispensing Deduction via FEFO]
    E6 -->|qty = 0| E7[Batch → depleted]
    E2 --> E8[Pharmacist: Dispose Batch → disposed]
    E2 --> E9[Pharmacist: Adjust Stock Count]
    E9 -->|qty = 0| E7
    E9 -->|qty > 0| E10[Batch → active]
    E2 --> E11[Dashboard: Expiry/Low-Stock/Out-of-Stock Alerts]
    E2 --> E12[Export CSV Report]

    E13["Supplier management"] -.-> E2
    E14["Batch undo-dispose / restore"] -.-> E8
    style E13 stroke-dasharray: 5 5
    style E14 stroke-dasharray: 5 5
```

> **Findings**:
> - No supplier management entity exists (Missing)
> - Disposed batches cannot be restored/un-disposed (Missing)
> - No low-stock threshold configuration per medicine — hard-coded at 20 units (Loophole)

---

## Flow F: Other Branches

### F.1 Follow-up Visit Tracking

| Step | Actor | Endpoint | Status | Completeness |
|------|-------|---------|--------|-------------|
| 1 | Doctor | `completeConsultation` (is_followup_needed=true) | Consultation gets `followup_date`, `followup_doctor_id` | Complete |
| 2 | System | Patient model updated | `next_followup_date`, `previous_doctor_id` | Complete |
| 3 | Receptionist | `FollowUpController::index` | View overdue/due/upcoming follow-ups | Complete |
| 4 | Receptionist | `FollowUpController::reschedule` | Update follow-up date | Complete |
| 5 | Receptionist | `FollowUpController::fulfill` | Mark follow-up completed | Complete |
| 6 | System | On next visit, auto-detect follow-up routing | Routes to original doctor if present | Complete |
| 7 | Patient | Public appointment form | Can flag `is_follow_up` + verify with reference # | Complete |

### F.2 Clinical Nurse Consultation Path

Clinical nurses handle light-severity cases independently:

| Step | Actor | Endpoint | Completeness |
|------|-------|---------|-------------|
| 1 | Nurse | `NurseController::startConsultation` | Complete |
| 2 | Nurse | `NurseController::completeConsultation` | Complete |
| 3 | Nurse | `NurseController::forwardToDoctor` | Complete |
| 4 | Nurse | `NurseController::storeAncillaryRequest` | Complete |
| 5 | Nurse | `NurseController::cancelConsultation` | Complete |
| 6 | Nurse | `NurseController::storeAddendum` | Complete |
| 7 | Nurse | `NurseController::cancelByPrescriber` | Complete |

> Nurse has nearly identical capabilities to Doctor for consultations they own.

### F.3 Referral System

**Status: NOT IMPLEMENTED**

No referral model, controller, or routes exist. The doctor's cancel-consultation form mentions "Referred" as a reason string, but there is no structured referral record, destination facility tracking, referral letter generation, or receiving-facility workflow.

### F.4 Inter-Staff Chat

| Feature | Endpoint | Completeness |
|---------|---------|-------------|
| List conversations | `ChatController::index` | Complete |
| Create conversation | `ChatController::store` | Complete |
| View messages | `ChatController::messages` | Complete |
| Send message | `ChatController::sendMessage` | Complete |
| Mark as read | `ChatController::markAsRead` | Complete |
| List staff users | `ChatController::users` | Complete |
| Unread count | `ChatController::unreadCount` | Complete |

### F.5 Patient Records & Retention

| Feature | Evidence | Completeness |
|---------|---------|-------------|
| Patient master list (Frontdesk) | `PatientController::index` | Complete |
| Patient detail view | `PatientController::show` | Complete |
| Patient records (Admin) | `AdminController::patientRecordsIndex` | Complete |
| Patient detail (Admin) | `AdminController::showPatient` | Complete |
| ITR Print | `AdminController::printItr` | Complete |
| Ancillary Print | `AdminController::printAncillary` | Complete |
| Retention management | `AdminController::retentionIndex`, `extendRetention`, `deleteRetention` | Complete |
| Auto-expiry (10 years) | Patient model `expires_at`, `Prunable` trait | Complete |
| Soft delete | Patient `SoftDeletes` trait | Complete |
| Archive restore | `ArchiveController::restore` | Complete |
