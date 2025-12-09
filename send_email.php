<?php
function sendOTPEmail($to, $otp) {
    $subject = "Your ZeroTrustBank OTP Code";
    $message = "Your OTP is: $otp\nThis code expires in 1 minute.";

    $headers = "From: mazenwael5115@gmail.com";

    // send email
    return mail($to, $subject, $message, $headers);
}

/**
 * Sends a generic email with the given subject and message
 * 
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $message Email body
 * @return bool Returns true if the email was accepted for delivery, false otherwise
 */
function sendEmail($to, $subject, $message) {
    $headers = "From: mazenwael5115@gmail.com" . "\r\n" .
               "Reply-To: mazenwael5115@gmail.com" . "\r\n" .
               "X-Mailer: PHP/" . phpversion();
    
    // Send email with HTML support
    $headers .= "\r\nMIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    // Convert line breaks to HTML line breaks
    $message = nl2br(htmlspecialchars($message));
    
    // Add HTML wrapper
    $htmlMessage = "
    <!DOCTYPE html>
    <html>
    <head>
        <title>$subject</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
            .content { padding: 20px; }
            .footer { margin-top: 20px; padding: 10px; text-align: center; font-size: 12px; color: #6c757d; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>ZeroTrustBank</h2>
            </div>
            <div class='content'>
                $message
            </div>
            <div class='footer'>
                This is an automated message. Please do not reply to this email.
            </div>
        </div>
    </body>
    </html>
    ";
    
    return mail($to, $subject, $htmlMessage, $headers);
}
?>
