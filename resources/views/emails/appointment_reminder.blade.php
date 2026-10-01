<!DOCTYPE html>
<html>
<head>
    <title>Appointment Reminder</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
        <!-- Header -->
        <div style="background-color: #0d9488; padding: 20px; text-align: center;">
            <h1 style="color: #ffffff; margin: 0; font-size: 24px;">RHU Connect</h1>
        </div>
        
        <!-- Content -->
        <div style="padding: 30px;">
            <p style="margin-top: 0; font-size: 18px; font-weight: bold; color: #0d9488;">Upcoming Appointment Reminder</p>
            <p>Dear {{ $appointment->first_name }},</p>
            <p>This is a friendly reminder that you have an upcoming consultation scheduled for <strong>tomorrow</strong> at the Rural Health Unit. Below are the details:</p>
            
            <div style="background-color: #f0fdfa; border: 1px solid #ccfbf1; border-radius: 6px; padding: 20px; margin: 20px 0;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 5px 0; color: #64748b; font-size: 14px; width: 40%;">Reference No:</td>
                        <td style="padding: 5px 0; font-weight: bold; color: #0f766e;">{{ $appointment->reference_number }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0; color: #64748b; font-size: 14px;">Patient Name:</td>
                        <td style="padding: 5px 0; font-weight: bold; color: #334155;">{{ $appointment->first_name }} {{ $appointment->last_name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0; color: #64748b; font-size: 14px;">Date:</td>
                        <td style="padding: 5px 0; font-weight: bold; color: #334155;">{{ \Carbon\Carbon::parse($appointment->preferred_date)->format('F d, Y (l)') }}</td>
                    </tr>
                    @if($appointment->preferred_time)
                    <tr>
                        <td style="padding: 5px 0; color: #64748b; font-size: 14px;">Arrival Time Slot:</td>
                        <td style="padding: 5px 0; font-weight: bold; color: #0d9488;">{{ $appointment->preferred_time }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding: 5px 0; color: #64748b; font-size: 14px;">Service Type:</td>
                        <td style="padding: 5px 0; font-weight: bold; color: #334155;">{{ ucfirst($appointment->type) }}</td>
                    </tr>
                    @if($appointment->complaint)
                    <tr>
                        <td style="padding: 5px 0; color: #64748b; font-size: 14px;">Chief Complaint:</td>
                        <td style="padding: 5px 0; color: #334155;">{{ $appointment->complaint }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            
            <div style="background-color: #f8fafc; border-left: 4px solid #0d9488; padding: 15px; margin: 20px 0;">
                <p style="margin: 0 0 8px 0; font-weight: bold; color: #0f766e;">📌 Reminders for your visit:</p>
                <ul style="margin: 0; padding-left: 20px; font-size: 14px; color: #475569;">
                    <li>Please arrive <strong>15–30 minutes before</strong> your scheduled arrival time for vital signs checking.</li>
                    <li>Bring a valid government-issued ID or school ID (for pediatric patients).</li>
                    <li>Bring your PhilHealth Member Data Record (MDR) or PhilHealth ID if applicable.</li>
                    <li>Wear a face mask if you are experiencing respiratory symptoms (cough, colds, fever).</li>
                </ul>
            </div>
            
            <p style="font-size: 13px; color: #64748b;">
                Cannot make it? You can manage or reschedule your appointment online using your reference number:
            </p>
            <div style="text-align: center; margin: 20px 0;">
                <a href="{{ route('appointment.manage') }}" style="background-color: #0d9488; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block; font-size: 13px;">Manage or Reschedule Booking</a>
            </div>
            
            <p style="margin-top: 30px; font-size: 14px; color: #64748b;">
                Warm regards,<br>
                Rural Health Unit Team
            </p>
        </div>
        
        <!-- Footer -->
        <div style="background-color: #f8fafc; padding: 15px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0;">
            &copy; {{ date('Y') }} Rural Health Unit. All rights reserved.
        </div>
    </div>
</body>
</html>
