<!DOCTYPE html>
<html>
<head>
    <title>Verification Code</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
        <!-- Header -->
        <div style="background-color: #0d9488; padding: 20px; text-align: center;">
            <h1 style="color: #ffffff; margin: 0; font-size: 24px;">RHU Connect</h1>
        </div>
        
        <!-- Content -->
        <div style="padding: 30px;">
            <p style="margin-top: 0;">Hello,</p>
            <p>You satisfy a request to book an appointment. Please use the following One-Time Password (OTP) to verify your email address and complete your booking:</p>
            
            <div style="background-color: #f0fdfa; border: 1px dashed #14b8a6; padding: 15px; text-align: center; border-radius: 6px; margin: 20px 0;">
                <span style="font-size: 32px; font-weight: bold; color: #0f766e; letter-spacing: 5px;">{{ $otp }}</span>
            </div>
            
            <p>This code will expire in 10 minutes.</p>
            <p>If you did not request this, please ignore this email.</p>
            
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
