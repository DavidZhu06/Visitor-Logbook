<?php

session_start(); // <-- Needed to use $_SESSION

// Clear any old data from other sign-in types 
 //Prevent data from Interview Form from leaking into Guest send-mail because sometimes previous interview data was showing up in the email sent to the guest
unset($_SESSION['contact'], $_SESSION['company'], $_SESSION['IDCI_Contact'], $_SESSION['email_contact'], $_SESSION['passnumber'], $_SESSION['signature_path']);

// Load config file (database credentials)
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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        date_default_timezone_set('America/Vancouver');
        $first_name = htmlspecialchars(trim($_POST['first_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $last_name = htmlspecialchars(trim($_POST['last_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $company = htmlspecialchars(trim($_POST['company'] ?? ''), ENT_QUOTES, 'UTF-8');
        $IDCI_Contact = htmlspecialchars(trim($_POST['IDCI_Contact'] ?? ''), ENT_QUOTES, 'UTF-8');
        $email_contact = htmlspecialchars(trim($_POST['email_contact'] ?? ''), ENT_QUOTES, 'UTF-8');
        $passnumber = htmlspecialchars(trim($_POST['passnumber'] ?? ''), ENT_QUOTES, 'UTF-8');
        $sign_in_time = date('Y-m-d H:i:s'); 

        /*
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $company = $_POST['company'];
        $reason = $_POST['reason'];
        $email_contact = $_POST['email_contact'];
        $passnumber = $_POST['passnumber'];
        $sign_in_time = date('Y-m-d H:i:s');
        */

        $stmt = $conn->prepare("INSERT INTO guests (first_name, last_name, company, IDCI_Contact, email_contact, passnumber, sign_in_time) 
                                VALUES (:first_name, :last_name, :company, :IDCI_Contact, :email_contact, :passnumber, :sign_in_time)");
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':company', $company);
        $stmt->bindParam(':IDCI_Contact', $IDCI_Contact);
        $stmt->bindParam(':email_contact', $email_contact);
        $stmt->bindParam(':passnumber', $passnumber);
        $stmt->bindParam(':sign_in_time', $sign_in_time);

        $stmt->execute();

        $_SESSION['guest_id'] = $conn->lastInsertId(); //This saves the newly inserted guest ID into the PHP session under the ky 'guest_id'
        $_SESSION['sign_in_type'] = 'guest'; //Stores string 'guestr' in the session under the key 'sign_in_type' to indicate the type of user signed in

        // Store sign_in_time in session for use in signature filename
        $_SESSION['sign_in_time'] = $sign_in_time;

        // Store for later email
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;
        $_SESSION['email'] = $email_contact; 
        $_SESSION['company'] = $company;
        $_SESSION['IDCI_Contact'] = $IDCI_Contact;
        $_SESSION['email_contact'] = $email_contact;
        $_SESSION['passnumber'] = $passnumber;

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
