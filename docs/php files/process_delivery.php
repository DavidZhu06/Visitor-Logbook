<?php

session_start(); // <-- Needed to use $_SESSION

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
    // Create PDO connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get and sanitize form data (filter_sanitize_string is deprecated as of PHP 8.1)
        date_default_timezone_set('America/Vancouver');
        $first_name = htmlspecialchars(trim($_POST['first_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $last_name = htmlspecialchars(trim($_POST['last_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $company = htmlspecialchars(trim($_POST['company'] ?? ''), ENT_QUOTES, 'UTF-8');
        $custom_company = htmlspecialchars(trim($_POST['custom_company'] ?? ''), ENT_QUOTES, 'UTF-8');
        $recipient = htmlspecialchars(trim($_POST['recipient'] ?? ''), ENT_QUOTES, 'UTF-8');
        $sign_in_time = date('Y-m-d H:i:s');

        // Determine the company name to store
        $company_name = $company;
        if ($company === "Other") {
            if (empty($custom_company)) {
                throw new Exception("Please enter a company name for 'Other'.");
            }
            $company_name = $custom_company;
        }

        // Prepare and execute SQL statement
        $stmt = $conn->prepare("INSERT INTO deliveries (first_name, last_name, company, recipient, sign_in_time) 
                                VALUES (:first_name, :last_name, :company, :recipient, :sign_in_time)");
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':company', $company_name);
        $stmt->bindParam(':recipient', $recipient);
        $stmt->bindParam(':sign_in_time', $sign_in_time);

        $stmt->execute();

        $_SESSION['guest_id'] = $conn->lastInsertId(); //This saves the newly inserted Delivery ID into the PHP session under the ky 'guest_id'
        $_SESSION['sign_in_type'] = 'delivery'; //Stores string 'delivery' in the session under the key 'sign_in_type' to indicate the type of user signed in

        // Store sign_in_time in session for use in signature filename - techinically not needed for deliveries
        $_SESSION['sign_in_time'] = $sign_in_time;

        // Redirect to confirmation page
        header("Location: ../html files/SignInEndScreen.html");
        exit();
    }
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>