<?php
// Database connection settings
$host = 'localhost';
$dbname = 'visitorlogbook_db';
$username = 'root';
$password = 'IDCIp@ssDZ2025!';

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get signature data from POST
    if (isset($_POST['signature']) && !empty($_POST['signature'])) {
        $signature = $_POST['signature'];
        
        // Remove data URL prefix if present
        $signature = preg_replace('/^data:image\/(png|jpeg);base64,/', '', $signature);

        // Save as PNG file
        $file_path = 'signatures/signature_' . time() . '.png';
        if (!file_exists('signatures')) {
            mkdir('signatures', 0755, true);
        }
        file_put_contents($file_path, base64_decode($signature));
        
        // Prepare SQL statement
        $stmt = $pdo->prepare("INSERT INTO signatures (signature_data, time_of_signature) VALUES (:signature, NOW())");
        
        // Execute with signature data
        $stmt->execute([
            ':signature' => $signature
        ]);

        // Redirect to parking page
        header('Location: ./html files/ParkingPage.html');
        exit;
    } else {
        //Redirect back to policy info if not signature is provided 
        header('Location: ./html files/PolicyInfo.html');
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