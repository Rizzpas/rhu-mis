<?php

namespace App\Traits;

use App\Models\Medicine;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\User;
use App\Notifications\NewPrescriptionNotification;
use App\Events\QueueUpdated;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Notification;

trait InteractsWithPrescriptions
{
    /**
     * Resolve prescription items from the submitted form data.
     * Returns ['rhu' => [...], 'otc' => [...], 'reclassified' => [...]]
     */
    protected function resolvePrescriptionItems(array $items): array
    {
        $rhu = [];
        $otc = [];
        $reclassified = [];

        foreach ($items as $item) {
            $isOtc = $item['is_otc'] ?? false;
            $medicineId = $item['medicine_id'] ?? null;
            $medicineName = $item['medicine_name'] ?? '';

            $matchedMedicine = null;

            if ($medicineId) {
                $matchedMedicine = Medicine::where('is_active', true)->find($medicineId);
            }

            if (! $matchedMedicine && $medicineName) {
                // Strip parenthetical suffix, e.g. "Paracetamol (Tablet)" → "Paracetamol"
                $cleanName = trim(preg_replace('/\s*\([^)]*\)$/', '', $medicineName));
                // Strip dosage patterns, e.g. "Mefenamic Acid 500mg" → "Mefenamic Acid"
                $baseName = trim(preg_replace('/\s+\d+\s*(mg|mcg|ml|g|iu)\b/i', '', $cleanName));

                $matchedMedicine = Medicine::where('is_active', true)
                    ->where(function ($q) use ($medicineName, $cleanName, $baseName) {
                        $q->where('name', $medicineName)
                            ->orWhere('generic_name', $medicineName)
                            ->orWhere('name', $cleanName)
                            ->orWhere('generic_name', $cleanName)
                            ->orWhere('name', $baseName)
                            ->orWhere('generic_name', $baseName);
                    })
                    ->first();
            }

            $prescriptionItem = [
                'medicine_id' => $matchedMedicine?->id,
                'medicine_name' => $medicineName,
                'dosage' => $item['dosage'] ?? null,
                'frequency' => $item['frequency'] ?? null,
                'duration' => $item['duration'] ?? null,
                'quantity' => $item['quantity'] ?? null,
            ];

            if ($isOtc) {
                $otc[] = $prescriptionItem;
            } else {
                if ($matchedMedicine) {
                    $rhu[] = $prescriptionItem;
                } else {
                    // Forced to OTC - no matching inventory medicine
                    $otc[] = $prescriptionItem;
                    $reclassified[] = $prescriptionItem;
                }
            }
        }

        return ['rhu' => $rhu, 'otc' => $otc, 'reclassified' => $reclassified];
    }

    /**
     * Format prescription text for human-readable display/storage.
     */
    protected function formatPrescriptionText(array $rhu, array $otc): string
    {
        $lines = [];

        if (! empty($rhu)) {
            $lines[] = 'Rx (RHU Inventory):';
            foreach ($rhu as $item) {
                $lines[] = "- {$item['medicine_name']} — {$item['dosage']} • {$item['frequency']} • {$item['duration']} — Qty: {$item['quantity']}";
            }
            $lines[] = '';
        }

        if (! empty($otc)) {
            $lines[] = 'OTC / External Purchase:';
            foreach ($otc as $item) {
                $lines[] = "- {$item['medicine_name']} — {$item['dosage']} • {$item['frequency']} • {$item['duration']} — Qty: {$item['quantity']}";
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Record the prescription and its items.
     * Returns the Prescription or null if no RHU items.
     */
    protected function recordPrescription($consultation, User $author, array $rhu, array $otc, string $text): ?Prescription
    {
        if (empty($rhu)) {
            return null;
        }

        $expiryDays = \App\Models\SiteSetting::get('pharmacy.prescription_expiry_days', 3);
        $expiresAt = now()->addDays($expiryDays);

        $prescription = Prescription::create([
            'consultation_id' => $consultation->id,
            'patient_id' => $consultation->patient_id,
            'doctor_id' => $author->id,
            'status' => 'pending',
            'expires_at' => $expiresAt,
        ]);

        foreach ($rhu as $item) {
            PrescriptionItem::create([
                'prescription_id' => $prescription->id,
                'medicine_id' => $item['medicine_id'],
                'medicine_name' => $item['medicine_name'],
                'dosage' => $item['dosage'],
                'frequency' => $item['frequency'],
                'duration' => $item['duration'],
                'quantity' => $item['quantity'],
                'dispensed_quantity' => 0,
            ]);
        }

        return $prescription;
    }

    /**
     * Notify pharmacy about a new prescription.
     */
    protected function notifyPharmacy(Prescription $prescription, User $author, $consultation): void
    {
        $pharmacists = User::where('role', 'pharmacy')->get();

        if ($pharmacists->isEmpty()) {
            return;
        }

        $patient = $consultation->patient;
        if (! $patient) {
            return;
        }

        Notification::send($pharmacists, new NewPrescriptionNotification(
            $patient->first_name . ' ' . $patient->last_name,
            $author->id,
            $prescription->id
        ));

        broadcast(new QueueUpdated('New prescription', 'pharmacy'));
    }
}