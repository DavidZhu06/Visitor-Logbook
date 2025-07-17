<?php
ob_start(); // Prevents any accidental output before headers
session_start(); // Access session variables

$config = require __DIR__ . '/../config.php';

$host = $config['host'];
$dbname = $config['dbname'];
$username = $config['username'];
$password = $config['password'];


try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get signature data from POST
    if (isset($_POST['signature']) && !empty($_POST['signature'])) {
        $signature = $_POST['signature'];
        
        // Remove data URL prefix if present
        $signature = preg_replace('/^data:image\/(png|jpeg);base64,/', '', $signature);

        // Save as PNG file (for download/debug/logging - Optional)
        $sign_in_time = $_SESSION['sign_in_time'] ?? date('Y-m-d H:i:s');
        $date = str_replace([':', ' '], ['-', '_'], $sign_in_time); // Replace : and space with - and _


        $file_path = 'signatures/signature_' . $date . '.png'; //$date .  instead of time()
        if (!file_exists('signatures')) {
            mkdir('signatures', 0755, true);
        }
        file_put_contents($file_path, base64_decode($signature));



        $_SESSION['signature_path'] = $file_path;



        
        // Lookup table based on sign-in type
        $table_map = [
            'interview' => 'interviews',
            'contractor' => 'contractors',
            'delivery' => 'deliveries',
            'guest' => 'guests'
        ];

        $sign_in_type = $_SESSION['sign_in_type'] ?? null;
        $guest_id = $_SESSION['guest_id'] ?? null;

        if ($sign_in_type && $guest_id && isset($table_map[$sign_in_type])) {
            $table = $table_map[$sign_in_type];

            // IMPORTANT: Column `signature_data` must exist in each table
            $stmt = $pdo->prepare("UPDATE $table SET signature_data = :signature WHERE id = :id");
        
            // Execute with signature data
            $stmt->execute([
                ':signature' => $signature,
                ':id' => $guest_id
            ]);


            require_once 'send-mail.php';

            $formData = [
                'first_name' => $_SESSION['first_name'] ?? '',
                'last_name' => $_SESSION['last_name'] ?? '',
                'company' => ($sign_in_type === 'interview') ? 'N/A' : ($_SESSION['company'] ?? ''),
                'service' => ($sign_in_type === 'interview') ? 'N/A' : ($_SESSION['service'] ?? ''),
                'email_contact' => $_SESSION['contact'] ?? '',
                'passnumber' => $_SESSION['passnumber'] ?? '',
                'sign_in_time' => $_SESSION['sign_in_time'] ?? '',
                'IDCI_Contact' => ($_SESSION['sign_in_type'] === 'interview') ? ($_SESSION['contact'] ?? '') : '',

            ];

            $recipientEmail = $_SESSION['email_contact'] ?? '';
            $recipientName = trim($formData['first_name'] . ' ' . $formData['last_name']);

            if (!sendEmail($recipientEmail, $recipientName, $formData)) {
                error_log("Failed to send email to $recipientEmail at " . date('Y-m-d H:i:s'));
            }










            // Redirect to parking page
            header('Location: ../html files/ParkingPage.html');
            exit;
        } else {
            // Redirect back to policy info if no signature is provided 
            header('Location: ../html files/PolicyInfo.html');
            exit;
        }
    } else {
        // Redirect back to policy info if no signature is provided 
        header('Location: ../html files/PolicyInfo.html');
        exit;
    }
} catch (PDOException $e) {
    // Handle database errors
    http_response_code(500);
    echo "Database error: " . $e->getMessage();
    //header('Location: ../html files/ParkingPage.html'); // Redirect to parking page on error
} catch (Exception $e) {
    // Handle other errors
    http_response_code(400);
    echo "Error: " . $e->getMessage();
    //header('Location: ../html files/ParkingPage.html');
}
?>