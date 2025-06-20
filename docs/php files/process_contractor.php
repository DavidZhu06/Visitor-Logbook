<?php

session_start(); // <-- Needed to use $_SESSION

// Database connection
$host = "localhost";
$dbname = "visitorlogbook_db";
$username = "root";
$password = "IDCIp@ssDZ2025!";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
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