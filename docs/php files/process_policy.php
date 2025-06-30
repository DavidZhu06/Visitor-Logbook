<?php
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
} catch (Exception $e) {
    // Handle other errors
    http_response_code(400);
    echo "Error: " . $e->getMessage();
}
?>