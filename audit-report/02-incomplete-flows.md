# Phase 2: Incomplete and Open-Ended Flow Detection

---

## Flow Completeness Matrix

| Flow | Step | Status | Has End State? | Evidence | What Is Needed |
|------|------|--------|---------------|----------|----------------|
| A | Appointment approval | **Missing** | N/A | `approved` in enum but no controller sets `pending` → `approved` | Add manual or auto-approve endpoint; or remove `approved` from enum and treat `pending` as bookable |
| A | Appointment auto-approval | **Missing** | N/A | No code transitions `pending` → `approved` automatically | Either auto-approve on creation or add staff approval screen |
| A | Rescheduling slot release | **Partial** | Yes | `PublicController::rescheduleAppointment` changes status but doesn't verify slot availability for new date | Add slot availability check on reschedule |
| B | Walk-in pedia cap enforcement | Complete | Yes | `TriageController::store` L255-268 and `RegistrationController::storeVisit` L708-715 | — |
| C | Lab result → doctor review | **Partial** | Partially | Consultation → `results_ready` auto-synced, but no explicit "Reviewed" status on ancillary request | Consider adding `Reviewed` status on AncillaryRequest to track doctor acknowledgement |
| C | Ancillary request no-show cleanup | **Partial** | No | Lab dashboard shows past-day pending as "no-show" in archive tab, but status stays `Pending` | Add scheduled command or auto-archive for stale pending requests |
| D | Prescription expiry notification | **Missing** | N/A | `ExpireStalePrescriptions` auto-expires but doesn't notify patient or doctor | Add notification to doctor/patient when prescription expires |
| D | Substitution workflow | **Missing** | N/A | No endpoint or UI for pharmacist to substitute medicine | Add substitution request → doctor approval flow |
| D | Partial dispense return to doctor | **Missing** | N/A | `partially_dispensed` stays in pharmacy queue but doctor is not notified | Add notification to doctor when partial dispense occurs |
| E | Supplier tracking | **Missing** | N/A | No supplier table, model, or routes | Add supplier management with batch-to-supplier linking |
| E | Batch restore after disposal | **Missing** | N/A | `disposed` is terminal; no undo or restore route | Add restore-from-disposed with audit trail |
| E | Low-stock threshold per medicine | **Missing** | N/A | Hard-coded 20 units in `PharmacyController::dashboard` L73 | Add configurable threshold per medicine |
| E | Expiry auto-disposal | **Missing** | N/A | Expired batches flagged in UI but not auto-disposed | Add scheduled command to auto-mark expired batches |
| F | Referral system | **Missing** | N/A | No model, controller, or routes | Full referral module: create, track, receive, close |
| F | Visit summary / discharge slip | **Missing** | N/A | No endpoint generates a patient-facing visit summary | Add printable visit summary for patient |
| A/B | End-of-day queue cleanup | **Missing** | N/A | No scheduled command closes stale queued consultations | Add daily cleanup for unclosed queue entries |
| A | Appointment `done` status from non-consultation path | **Partial** | Yes | `done` only set when consultation completes (L305); no-show appointments are `no_show` not `done` | Acceptable design |

## Dead and Unreachable Statuses

| Entity | Status | Issue | Evidence |
|--------|--------|-------|----------|
| Consultation | `done` | **Dead status**: Referenced in `whereIn(['completed', 'done', ...])` queries but no code ever transitions a consultation to `done`. Both `completed` and `done` are treated as equivalent terminals. | DoctorController L112, L202, L225 (read); no write found |
| Appointment | `approved` | **No setter found**: The `approved` status is in the DB enum and referenced in views/controllers for display, but no endpoint transitions `pending` → `approved`. The `FixStuckAppointments` command references it. | Base migration L97 (enum), no controller write |
| Queue | — | No explicit `done` or `closed` status. Queues stay `Waiting` if consultation is never started. | Queue model only has `Waiting`, `Calling`, `Completed`, `Cancelled` |

## Open-Ended Records (Records That Can Stay Non-Final Forever)

| Record Type | Non-Final Status | Auto-Close Mechanism | Manual Close? | Risk |
|------------|-----------------|---------------------|--------------|------|
| **Consultation** | `queued` | None | Doctor can cancel | A consultation queued on Monday stays `queued` forever if never started. No end-of-day cleanup. |
| **Consultation** | `active` | None | Doctor can complete/cancel | If doctor navigates away without completing, stays `active` indefinitely |
| **Consultation** | `awaiting_results` | None | Doctor can cancel | If lab never processes, consultation stays in limbo forever |
| **Queue** | `Waiting` | None | Not directly closeable | Queue entries from previous days remain `Waiting` forever |
| **Queue** | `Calling` | None | Not directly closeable | If doctor starts but doesn't complete, queue stays `Calling` |
| **PreTriage** | `waiting` | `app:cleanup-vitals` command | Nurse can cancel | ✅ Has cleanup command, but need to verify its logic |
| **Appointment** | `pending` | `ProcessAppointmentNoShows` + `FixStuckAppointments` | Patient/Staff can cancel | ✅ Has auto no-show and fix-stuck commands |
| **Appointment** | `arrived` / `triaged` | `FixStuckAppointments` | Staff can cancel | ✅ Has fix-stuck command |
| **Prescription** | `pending` | `ExpireStalePrescriptions` command | Doctor/Pharmacist can cancel | ✅ Has auto-expiry |
| **Prescription** | `partially_dispensed` | None found | Pharmacist can cancel | Partial prescriptions may stay open indefinitely if patient never returns |
| **AncillaryRequest** | `Pending` | None (archive tab shows them as no-show) | Lab can cancel/archive | Past-day pending requests are shown as "no-show" in archive tab but status is never changed |
| **AncillaryRequest** | `Specimen Collected` / `In Progress` | None | Lab can cancel | Stuck mid-processing requests have no timeout |
| **MedicineBatch** | `active` with qty=0 | Not auto-changed to `depleted` | Adjust stock | Dispensing sets `depleted` but stock adjustment to 0 also sets `depleted` (L1020). Edge: batch could theoretically reach 0 via concurrent decrement without the status check |
| **MedicineBatch** | `active` with expired date | No auto-disposal | Pharmacist can dispose | Expired batches remain `active` status, only excluded by date filter in queries |

## Missing Handoffs

| From Role | Creates Task For | Handoff Mechanism | Issue |
|-----------|-----------------|-------------------|-------|
| Doctor | Lab Staff | AncillaryRequest + broadcast event | ✅ Complete — lab dashboard shows pending requests |
| Doctor | Pharmacist | Prescription + notification + broadcast | ✅ Complete |
| Lab Staff | Doctor | `syncConsultationReadiness` + broadcast | ✅ Complete — consultation moves to `results_ready` |
| Receptionist | Vitals Nurse | PreTriage display | ⚠️ **Partial** — No notification sent to vitals nurse; relies on dashboard polling/broadcast |
| Vitals Nurse | Receptionist | PreTriage `waiting` | ✅ Complete — front desk sees waiting list with polling |
| Doctor | Receptionist (follow-up) | `followup_date` on consultation | ✅ Complete — FollowUpController exists |
| System | Doctor (expired Rx) | No notification | ❌ **Missing** — doctor not notified when their prescription expires |
| System | Doctor (rejected lab) | No notification | ❌ **Missing** — doctor only sees rejection when checking dashboard or waiting-results page |
| Doctor | External facility (referral) | Nothing | ❌ **Missing** — no referral system |

## Half-Built Features

| Feature | Frontend | Backend | Issue |
|---------|---------|---------|-------|
| Appointment approval | Status displayed in views | No approve endpoint | Appointments stay `pending` — no manual or auto approval flow |
| Medicine search API | Used in consultation form | `/api/medicines/search` exists | ⚠️ Accessible to ANY authenticated user (no role check beyond `auth`) — includes vitals nurse, front desk |
| Reports module | Admin analytics page exists | Analytics charts + CSV export | ⚠️ Limited to admin analytics; no per-module reports (lab turnaround, dispensing, daily census) |

## TODO/FIXME/Stub Analysis

| Pattern | Files Found | Notable Items |
|---------|------------|---------------|
| `// TODO` | Searched — none found in controllers | — |
| `// FIXME` | Searched — none found | — |
| Placeholder text | `RegistrationController` L467 empty block for Pediatric: `if ($validated['classification'] === 'Pediatric') { }` | Empty conditional — possible incomplete logic |
| Hard-coded data | `FacilityUnit` auto-seeds if count=0 (`PublicController::units` L73-75) | Auto-seeding in a controller is fragile |
| Demo mode | `SiteSetting::get('demo_mode')` in User model | Presence system bypasses schedule checks in demo mode |
