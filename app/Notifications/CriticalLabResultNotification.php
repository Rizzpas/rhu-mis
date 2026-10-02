<?php

namespace App\Notifications;

use App\Models\AncillaryRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CriticalLabResultNotification extends Notification
{
    use Queueable;

    public AncillaryRequest $ancillary;

    public string $remarks;

    /**
     * Create a new notification instance.
     */
    public function __construct(AncillaryRequest $ancillary, string $remarks = '')
    {
        $this->ancillary = $ancillary;
        $this->remarks = $remarks;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $patient = $this->ancillary->consultation ? $this->ancillary->consultation->patient : null;
        $patientName = $patient ? ($patient->first_name . ' ' . $patient->last_name) : 'Patient';

        return [
            'type' => 'critical_lab_result',
            'title' => 'CRITICAL LAB ALERT: ' . $this->ancillary->test_name,
            'message' => "CRITICAL VALUE ALERT for {$patientName} ({$this->ancillary->test_name}). Immediate clinical attention required! " . ($this->remarks ? "Remarks: {$this->remarks}" : ''),
            'ancillary_id' => $this->ancillary->id,
            'consultation_id' => $this->ancillary->consultation_id,
            'patient_id' => $patient ? $patient->patient_id : null,
            'patient_name' => $patientName,
            'test_name' => $this->ancillary->test_name,
            'is_critical' => true,
        ];
    }
}
