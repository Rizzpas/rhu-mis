<!DOCTYPE html>
<html>
<head>
    <title>Appointment Cancelled</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
        <!-- Header -->
        <div style="background-color: #e11d48; padding: 20px; text-align: center;">
            <h1 style="color: #ffffff; margin: 0; font-size: 24px;">RHU Connect</h1>
        </div>
        
        <!-- Content -->
        <div style="padding: 30px;">
            <p style="margin-top: 0; font-size: 18px; font-weight: bold; color: #e11d48;">Appointment Cancellation Notice</p>
            <p>Dear {{ $appointment->first_name }},</p>
            <p>This is to notify you that your consultation appointment has been cancelled. Below are the details of the cancelled booking:</p>
            
            <div style="background-color: #fff1f2; border: 1px solid #fecdd3; border-radius: 6px; padding: 20px; margin: 20px 0;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 5px 0; color: #64748b; font-size: 14px; width: 40%;">Reference No:</td>
                        <td style="padding: 5px 0; font-weight: bold; color: #334155;">{{ $appointment->reference_number }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0; color: #64748b; font-size: 14px;">Patient Name:</td>
                        <td style="padding: 5px 0; font-weight: bold; color: #334155;">{{ $appointment->first_name }} {{ $appointment->last_name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0; color: #64748b; font-size: 14px;">Scheduled Date:</td>
                        <td style="padding: 5px 0; font-weight: bold; color: #334155;">{{ \Carbon\Carbon::parse($appointment->preferred_date)->format('F d, Y (l)') }}</td>
                    </tr>
                    @if($appointment->preferred_time)
                    <tr>
                        <td style="padding: 5px 0; color: #64748b; font-size: 14px;">Arrival Time:</td>
                        <td style="padding: 5px 0; font-weight: bold; color: #334155;">{{ $appointment->preferred_time }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding: 5px 0; color: #64748b; font-size: 14px;">Service Type:</td>
                        <td style="padding: 5px 0; font-weight: bold; color: #334155;">{{ ucfirst($appointment->type) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0; color: #64748b; font-size: 14px;">Reason:</td>
                        <td style="padding: 5px 0; font-weight: bold; color: #e11d48;">{{ $reason }}</td>
                    </tr>
                </table>
            </div>
            
            <p>If this was unexpected or if you still need medical care, you may book a new appointment online through our portal or visit our Rural Health Unit during regular clinic hours.</p>
            
            <div style="text-align: center; margin: 25px 0;">
                <a href="{{ route('appointment.create') }}" style="background-color: #0d9488; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">Book a New Appointment</a>
            </div>
            
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
