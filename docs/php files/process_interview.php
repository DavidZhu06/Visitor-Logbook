<?php

session_start(); // <-- Needed to use $_SESSION

unset($_SESSION['company']);  // Interviews don't use this, keeps session data clean and relevant for user flow
unset($_SESSION['service']);  
unset($_SESSION['signature_path']); // Clear any previous signature path by clearing the old value from the current session since the PDF email signature was showing a previous signature 

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
        $first_name = htmlspecialchars(trim($_POST['first_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $last_name = htmlspecialchars(trim($_POST['last_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $IDCIContact = htmlspecialchars(trim($_POST['IDCI_Contact'] ?? ''), ENT_QUOTES, 'UTF-8');
        $email_contact = htmlspecialchars(trim($_POST['email_contact'] ?? ''), ENT_QUOTES, 'UTF-8');
        $passnumber = htmlspecialchars(trim($_POST['passnumber'] ?? ''), ENT_QUOTES, 'UTF-8');
        $sign_in_time = date('Y-m-d H:i:s');

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
        $_SESSION['guest_id'] = $conn->lastInsertId(); //This saves the newly inserted contractor ID into the PHP session under the ky 'guest_id'
        $_SESSION['sign_in_type'] = 'interview'; //Stores string 'contractor' in the session under the key 'sign_in_type' to indicate the type of user signed in

        

        // Store sign_in_time in session for use in signature filename
        $_SESSION['sign_in_time'] = $sign_in_time;

        // Store for later email
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;
        $_SESSION['email_contact'] = $email_contact;
        $_SESSION['passnumber'] = $passnumber;
        $_SESSION['contact'] = $IDCIContact;

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