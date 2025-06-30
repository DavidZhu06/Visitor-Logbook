<?php

// Load configuration
$config = require __DIR__ . '/../config.php';

// Database connection
$host = $config['host'];
$dbname = $config['dbname'];
$username = $config['username'];
$password = $config['password'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $firstName = $_POST['first-name'] ?? '';
    $lastName = $_POST['last-name'] ?? '';

    if (empty($firstName) || empty($lastName)) {
        throw new Exception("First name and last name are required.");
    }

    // Tables to search
    $tables = ['contractors', 'deliveries', 'guests', 'interviews'];

    $found = false;

    foreach ($tables as $table) {
        $stmt = $pdo->prepare("
            SELECT id FROM $table 
            WHERE first_name = :first_name 
              AND last_name = :last_name 
              AND sign_out_time IS NULL 
            ORDER BY sign_in_time DESC 
            LIMIT 1
        ");
        $stmt->execute([
            ':first_name' => $firstName,
            ':last_name' => $lastName
        ]);

        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($record) {
            $update = $pdo->prepare("UPDATE $table SET sign_out_time = NOW() WHERE id = :id");
            $update->execute([':id' => $record['id']]);
            $found = true;
            break; // stop after first match
        }
    }


    if ($found) {
        header('Location: ../html files/SignOutEndScreen.html');
        exit;
    } else {
        header('Location: ../html files/NoMatch.html');
        exit;
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo "Database error: " . $e->getMessage();
} catch (Exception $e) {
    http_response_code(400);
    echo "Error: " . $e->getMessage();
}
?>
