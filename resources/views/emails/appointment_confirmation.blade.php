<!DOCTYPE html>
<html>
<head>
    <title>Appointment Confirmed</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
        <!-- Header -->
        <div style="background-color: #0d9488; padding: 20px; text-align: center;">
            <h1 style="color: #ffffff; margin: 0; font-size: 24px;">RHU Connect</h1>
        </div>
        
        <!-- Content -->
        <div style="padding: 30px;">
            <p style="margin-top: 0; font-size: 18px; font-weight: bold; color: #0d9488;">Appointment Confirmation</p>
            <p>Dear {{ $appointment->first_name }},</p>
            <p>Your appointment request has been successfully received. Below are the details of your booking:</p>
            
            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 20px; margin: 20px 0;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 5px 0; color: #64748b; font-size: 14px; width: 40%;">Service Type:</td>
                        <td style="padding: 5px 0; font-weight: bold; color: #334155;">{{ ucfirst($appointment->type) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0; color: #64748b; font-size: 14px;">Date:</td>
                        <td style="padding: 5px 0; font-weight: bold; color: #334155;">{{ \Carbon\Carbon::parse($appointment->preferred_date)->format('F d, Y (l)') }}</td>
                    </tr>
                    @if($appointment->preferred_time)
                    <tr>
                        <td style="padding: 5px 0; color: #64748b; font-size: 14px;">Arrival Time:</td>
                        <td style="padding: 5px 0; font-weight: bold; color: #0d9488;">{{ $appointment->preferred_time }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding: 5px 0; color: #64748b; font-size: 14px;">Patient Name:</td>
                        <td style="padding: 5px 0; font-weight: bold; color: #334155;">{{ $appointment->first_name }} {{ $appointment->last_name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0; color: #64748b; font-size: 14px;">Reference No:</td>
                        <td style="padding: 5px 0; font-weight: bold; color: #334155;">{{ $appointment->reference_number }}</td>
                    </tr>
                </table>
            </div>
            
            @if($appointment->preferred_time)
            <p><strong style="color: #0d9488;">⚠️ Important:</strong> Please arrive <strong>30 minutes before</strong> your selected arrival time ({{ $appointment->preferred_time }}) for initial vital signs triage.</p>
            @else
            <p>Please arrive as early as possible, it is first come first served basis. If you need to cancel or reschedule, please contact us immediately.</p>
            @endif
            
            <p style="margin-top: 30px; font-size: 14px; color: #64748b;">
                Regards,<br>
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
