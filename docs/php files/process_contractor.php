<?php

session_start(); // <-- Needed to use $_SESSION

// Load configuration
$config = require __DIR__ . '/../config.php';

// Database connection
$host = $config['host'];
$dbname = $config['dbname'];
$username = $config['username'];
$password = $config['password'];

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        date_default_timezone_set('America/Vancouver');
        
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $company = $_POST['company'];
        $service = $_POST['service'];
        $email_contact = $_POST['contact'];
        $passnumber = $_POST['passnumber'];
        $sign_in_time = date('Y-m-d H:i:s');
    
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO contractors (first_name, last_name, company, service, email_contact, passnumber, sign_in_time) VALUES (:first_name, :last_name, :company, :service, :email_contact, :passnumber, :sign_in_time)");
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':company', $company);
        $stmt->bindParam(':service', $service);
        $stmt->bindParam(':email_contact', $email_contact);
        $stmt->bindParam(':passnumber', $passnumber);
        $stmt->bindParam(':sign_in_time', $sign_in_time);

        // Execute the statement
        $stmt->execute();

        $_SESSION['guest_id'] = $conn->lastInsertId();
        $_SESSION['sign_in_type'] = 'contractor';


        
        // Store sign_in_time in session for use in signature filename
        $_SESSION['sign_in_time'] = $sign_in_time;

        /*
        // Send email to the entered email address
        require_once 'send-mail.php';
        $recipientName = trim($first_name . ' ' . $last_name);
        // Attempt to send email, but don't interrupt flow on failure
        if (!sendEmail($email_contact, $recipientName, $first_name)) {
            error_log("Failed to send email to $email_contact at " . date('Y-m-d H:i:s'));
        }
        */

        // Store for later email
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;
        $_SESSION['company'] = $company;
        $_SESSION['service'] = $service;
        $_SESSION['email_contact'] = $email_contact;
        $_SESSION['passnumber'] = $passnumber;
        unset($_SESSION['signature_path']); // Clear any previous signature path

        // Redirect to PolicyInfo.html on success
        header("Location: ../html%20files/PolicyInfo.html");
        exit();
    } 
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

