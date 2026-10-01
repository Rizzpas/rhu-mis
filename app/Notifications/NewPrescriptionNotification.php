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

    public $type;

    /**
     * Create a new notification instance.
     */
    public function __construct($patientName, $doctorId, $prescriptionId, $type = 'pending')
    {
        $this->patientName = $patientName;
        $this->doctorId = $doctorId;
        $this->prescriptionId = $prescriptionId;
        $this->type = $type;
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
        $message = $this->type === 'expired'
            ? "Prescription #{$this->prescriptionId} for {$this->patientName} has expired."
            : "New prescription pending for {$this->patientName}.";

        $url = $this->type === 'expired'
            ? (auth()->user()?->role === 'nurse' ? route('nurse.dashboard') : route('doctor.dashboard'))
            : route('pharmacy.dashboard');

        return [
            'patient_name' => $this->patientName,
            'doctor_id' => $this->doctorId,
            'prescription_id' => $this->prescriptionId,
            'type' => $this->type,
            'message' => $message,
            'url' => $url,
        ];
    }
}
