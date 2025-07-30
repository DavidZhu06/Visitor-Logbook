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
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        date_default_timezone_set('America/Vancouver');
        // Get and sanitize form data (filter_sanitize_string is deprecated as of PHP 8.1)
        $first_name = filter_input(INPUT_POST, 'first_name', FILTER_DEFAULT) ? htmlspecialchars(trim($_POST['first_name']), ENT_QUOTES, 'UTF-8') : '';
        $last_name = filter_input(INPUT_POST, 'last_name', FILTER_DEFAULT) ? htmlspecialchars(trim($_POST['last_name']), ENT_QUOTES, 'UTF-8') : '';
        $IDCIContact = filter_input(INPUT_POST, 'IDCI_Contact', FILTER_DEFAULT) ? htmlspecialchars(trim($_POST['IDCI_Contact']), ENT_QUOTES, 'UTF-8') : '';
        $email_contact = filter_input(INPUT_POST, 'email_contact', FILTER_DEFAULT) ? htmlspecialchars(trim($_POST['email_contact']), ENT_QUOTES, 'UTF-8') : '';
        $passnumber = filter_input(INPUT_POST, 'passnumber', FILTER_DEFAULT) ? htmlspecialchars(trim($_POST['passnumber']), ENT_QUOTES, 'UTF-8') : '';
        $sign_in_time = date('Y-m-d H:i:s');
        
        /* Can also use the following if you prefer not to use filter_input:
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $IDCIContact = $_POST['IDCI_Contact'];
        $email_contact = $_POST['email_contact'];
        $passnumber = $_POST['passnumber'];
        $sign_in_time = date('Y-m-d H:i:s');
        */
    
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO interviews (first_name, last_name, IDCI_Contact, email_contact, passnumber, sign_in_time) VALUES (:first_name, :last_name, :IDCI_Contact, :email_contact, :passnumber, :sign_in_time)");
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':IDCI_Contact', $IDCIContact);
        $stmt->bindParam(':email_contact', $email_contact);
        $stmt->bindParam(':passnumber', $passnumber);
        $stmt->bindParam(':sign_in_time', $sign_in_time);


        // Execute the statement
        $stmt->execute();

        // Save the inserted ID and type to session
        $_SESSION['guest_id'] = $conn->lastInsertId();
        $_SESSION['sign_in_type'] = 'interview';

        

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
        $_SESSION['email_contact'] = $email_contact;
        $_SESSION['passnumber'] = $passnumber;
        $_SESSION['contact'] = $IDCIContact;


        unset($_SESSION['company']);  // Interviews don't use this
        unset($_SESSION['service']);  // Interviews don't use this
        unset($_SESSION['signature_path']); // Clear any previous signature path


        // Redirect to PolicyInfo.html on success
        header("Location: ../html files/PolicyInfo.html");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>