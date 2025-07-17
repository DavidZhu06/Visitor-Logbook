<?php
// Load PHPMailer classes manually (no autoloading)
require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmail($recipientEmail, $recipientName, $formData) {

require_once __DIR__ . '/../../vendor/autoload.php';

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


    $visitDate = date('l, F j, Y \a\t g:i A', strtotime($formData['sign_in_time']));


    // CONTENT
    $mail->isHTML(true); // Email body supports HTML
    $mail->Subject = 'Test Email from PHP on IIS';

    $mail->Body    = 'Hello, this is a test email sent securely via <b>smtp.office365.com</b><br><br> Dear ' . $recipientName . ', <br><br>Thank you for visiting Imperial Distributors Canada Inc on <b>' . htmlspecialchars($visitDate) . '</b>. We appreciate you taking the time to stop by our facility.<br><br>Attached please find a copy of your information you executed during your sign-in process.<br><br>Imperial Distributors Canada Inc. ';
    $mail->AltBody = 'Hello, this is a test email sent securely via smtp.office365.com
    Dear ' . $recipientName . ',

    Thank you for visiting Imperial Distributors Canada Inc on ' . $visitDate . '. We appreciate you taking the time to stop by our facility.

    Attached please find a copy of your information you executed during your sign-in process.

    Imperial Distributors Canada Inc.';


    // 🧾 Generate PDF using mPDF (ChatGPT)
    $mpdf = new \Mpdf\Mpdf();

    $signaturePath = $_SESSION['signature_path'] ?? null;

    if ($signaturePath && file_exists($signaturePath)) {
        $imageData = base64_encode(file_get_contents($signaturePath));
        $signatureImageTag = '<p><strong>SIGNATURE:</strong><br><img src="data:image/png;base64,' . $imageData . '" style="width:300px;" /></p>';
    }
    else {
            $signatureImageTag = '<p><strong>SIGNATURE:</strong> (Not Available)</p>';
    }

    $html = "
        <h2 style=\"text-align: center;\">{$recipientName} Sign-In Log</h2>
        <p><strong>Name:</strong> {$formData['first_name']} {$formData['last_name']}</p>
        <p><strong>Company:</strong> {$formData['company']}</p>
        <p><strong>Reason/Service:</strong> {$formData['service']}</p>
        <p><strong>Pass Number:</strong> {$formData['passnumber']}</p>
        <p><strong>IDCI Contact:</strong> " . (!empty($formData['IDCI_Contact']) ? htmlspecialchars($formData['IDCI_Contact']) : 'N/A') . "</p>
        <p><strong>Sign-In Time:</strong> {$formData['sign_in_time']}</p>
        <p>___________________________________________________</p>
        <p><strong>NON-DISCLOSURE POLICY:</strong> <br>I understand that during my time at Imperial Distributors Canada Inc. (IDCI), I may access confidential or proprietary information. I agree not to disclose any such information, including personal, private, operational, or trade secrets, to anyone during or after my relationship with the Company, unless authorized or legally required.
        I will not remove, copy, photograph, or otherwise record any documents or information, in any form, without written permission from IDCI. <strong>I will not photograph or otherwise record any information which I may have access to during my visit.</strong>
        I acknowledge that this information is valuable and not publicly known, and that improper disclosure could cause serious harm. IDCI may seek legal or injunctive action for any breach. Violations may result in legal consequences and termination of access to the Company or its premises.<br><br>

        <strong>SECURITY, SAFETY & GMP POLICY:</strong> <br>
        I acknowledge that I will: <br>
        <ol>
            <li>Be escorted by an IDCI team member while inside the building.</li>
            <li>Visibly display the visitor access card (if assigned) at all times while inside the building and return it upon my exit out of the building.</li>
            <li>Sign-in and sign-out of restricted areas where required.</li>
            <li>Maintain all exterior and interior doors in a closed position.</li>
            <li>Immediately evacuate to the nearest exit upon emergency alarm. Once evacuated, the building is not to be re-entered until the incident is officially declared as over.</li>
            <li>Not bring any food or beverage inside the warehouse.</li>
            <li>Not eat, drink, have chewing gum or others, or smoke inside the warehouse.</li>
            <li>Not handle any product if permitted inside the warehouse unless with prior permission.</li>
        </ol>
        </p>
        $signatureImageTag
    ";
    $mpdf->WriteHTML($html);

    // Save PDF to a file
    $rawSignInTime = $formData['sign_in_time'];
    $sanitizedTimestamp = date('Ymd_His', strtotime($rawSignInTime));
    $pdfPath = __DIR__ . "/../../pdfrecord/visitor_log_{$formData['first_name']}_{$formData['last_name']}.pdf_{$sanitizedTimestamp}.pdf";
    $mpdf->Output($pdfPath, \Mpdf\Output\Destination::FILE); 
    

    // 📎 Attach PDF to email
    $mail->addAttachment($pdfPath, 'SignInRecord.pdf');

    // SEND
    $mail->send();
    error_log("Email has been sent to {$recipientEmail}");


    // Deletes PDF - for david to note
    /*
    if (file_exists($pdfPath)) {
        unlink($pdfPath);
    }
    */

    return true; // Indicate success
} catch (Exception $e) {
        error_log("Message could not be sent. Error: {$mail->ErrorInfo}");
        return false; // Indicate failure
    }
}
?>
