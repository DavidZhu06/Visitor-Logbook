<?php

session_start(); // <-- Needed to use $_SESSION

$config = require __DIR__ . '/../config.php';

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
        $IDCI_Contact = $_POST['IDCI_Contact'];
        $email_contact = $_POST['email_contact'];
        $passnumber = $_POST['passnumber'];
        $sign_in_time = date('Y-m-d H:i:s');
    
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO interviews (first_name, last_name, IDCI_Contact, email_contact, passnumber, sign_in_time) VALUES (:first_name, :last_name, :IDCI_Contact, :email_contact, :passnumber, :sign_in_time)");
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':IDCI_Contact', $IDCI_Contact);
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



        // Send email to the entered email address
        require_once 'send-mail.php';
        $recipientName = trim($first_name . ' ' . $last_name);
        // Attempt to send email, but don't interrupt flow on failure
        if (!sendEmail($email_contact, $recipientName, $first_name)) {
            error_log("Failed to send email to $email_contact at " . date('Y-m-d H:i:s'));
        }




        // Redirect to PolicyInfo.html on success
        header("Location: ../html files/PolicyInfo.html");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>