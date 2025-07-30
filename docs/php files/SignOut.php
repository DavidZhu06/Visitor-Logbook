<?php

// Load configuration
$config = require __DIR__ . '/../config.php';

$env = 'production'; // Change this to 'production' when deploying

$dbConfig = $config[$env]; // Get the correct config

// Database connection
$host = $dbConfig['host'];
$dbname = $dbConfig['dbname'];
$username = $dbConfig['username'];
$password = $dbConfig['password'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $firstName = $_POST['first-name'] ?? '';
    $lastName = $_POST['last-name'] ?? '';
    $passnumber = trim($_POST['passnumber'] ?? '');

    if ((empty($firstName) || empty($lastName)) && empty($passnumber)) {
        throw new Exception("Please either enter First name and Last name OR your assigned Visitor Pass Number.");
    }

    // Tables to search
    $tables = ['contractors', 'deliveries', 'guests', 'interviews'];
    $tablesWithPass = ['contractors', 'guests', 'interviews']; // Tables that use passnumber

    $found = false;

    foreach ($tables as $table) {
        if (!empty($firstName) && !empty($lastName)) {
        // Search by name
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
        } elseif (in_array($table,$tablesWithPass)) {
            // Search by visitor pass
            $stmt = $pdo->prepare("
            SELECT id FROM $table 
            WHERE passnumber = :passnumber 
              AND sign_out_time IS NULL
              ORDER BY sign_in_time DESC
              LIMIT 1
            ");
        $stmt->execute([
            ':passnumber' => $passnumber
        ]);   
    }

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
