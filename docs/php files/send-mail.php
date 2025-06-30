<?php
// Load PHPMailer classes manually (no autoloading)
require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmail($recipientEmail, $recipientName, $firstName) {

$mailConfig = require __DIR__ . '/../mail_config.php';
    
$mail = new PHPMailer(true); // true = enables exceptions

try {
    // SERVER SETTINGS
    $mail->isSMTP(); // We’re using SMTP
    $mail->Host       = $mailConfig['smtp_server']; // Office 365 SMTP server
    $mail->SMTPAuth   = true; // Enable authentication
    $mail->Username   = $mailConfig['smtp_username']; // Replace with your Microsoft 365 email
    $mail->Password   = $mailConfig['smtp_password'];
    $mail->SMTPSecure = 'tls'; // Encryption method: TLS
    $mail->Port       = $mailConfig['smtp_port']; // TLS port for Office365

    // FROM and TO
    $mail->setFrom('mailsystem@idci.ca', 'Imperial Distributors Canada Inc.'); // Sender's email and name
    $mail->addAddress($recipientEmail, $recipientName); // Recipient's email and name

    // CONTENT
    $mail->isHTML(true); // Email body supports HTML
    $mail->Subject = 'Test Email from PHP on IIS';
    //$mail->Body    = 'Hello, this is a test email sent securely via <b>smtp.office365.com</b>.';
    //$mail->AltBody = 'Hello, this is a test email sent via smtp.office365.com.';


    $mail->Body    = 'Hello, this is a test email sent securely via <b>smtp.office365.com</b>. The following will act as placeholders until the email is fully implemented: <br><br> Dear ' . $recipientName . ', <br><br>Thank you for visiting Imperial Distributors Canada Inc. We truly appreciate you taking the time to stop by our facility.<br><br>Your presence is valued, and we hope your visit was both productive and enjoyable. If you have any feedback or questions about your experience, please do not hesitate to reach out to us.<br><br>We look forward to welcoming you back in the future!<br><br>Best regards,<br><br>Imperial Distributors Canada Inc. ';
    $mail->AltBody = 'Hello,' . $recipientName . 'this is a test email sent securely via <b>smtp.office365.com</b>. The following will act as placeholders until the email is fully implemented: <br><br> Dear Valued Visitor,<br><br>Thank you for visiting Imperial Distributors Canada Inc. We truly appreciate you taking the time to stop by our facility.<br><br>Your presence is valued, and we hope your visit was both productive and enjoyable. If you have any feedback or questions about your experience, please do not hesitate to reach out to us.<br><br>We look forward to welcoming you back in the future!<br><br>Best regards,<br><br>Imperial Distributors Canada Inc. ';

    // SEND
    $mail->send();
    return true; // Indicate success
} catch (Exception $e) {
        error_log("Message could not be sent. Error: {$mail->ErrorInfo}");
        return false; // Indicate failure
    }
}
?>
