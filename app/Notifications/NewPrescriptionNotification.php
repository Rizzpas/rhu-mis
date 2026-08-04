<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewPrescriptionNotification extends Notification
{
    use Queueable;

    public $patientName;

    public $doctorId;

    public $prescriptionId;

    /**
     * Create a new notification instance.
     */
    public function __construct($patientName, $doctorId, $prescriptionId)
    {
        $this->patientName = $patientName;
        $this->doctorId = $doctorId;
        $this->prescriptionId = $prescriptionId;
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
        return [
            'patient_name' => $this->patientName,
            'doctor_id' => $this->doctorId,
            'prescription_id' => $this->prescriptionId,
            'message' => "New prescription pending for {$this->patientName}.",
            'url' => route('pharmacy.dashboard'),
        ];
    }
}
