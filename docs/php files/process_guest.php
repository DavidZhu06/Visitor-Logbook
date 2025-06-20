<?php

session_start(); // <-- Needed to use $_SESSION


$host = "localhost";
$dbname = "visitorlogbook_db";
$username = "root";
$password = "IDCIp@ssDZ2025!";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $company = $_POST['company'];
        $reason = $_POST['reason'];
        $email_contact = $_POST['email_contact'];
        $passnumber = $_POST['passnumber'];
        $sign_in_time = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("INSERT INTO guests (first_name, last_name, company, reason, email_contact, passnumber, sign_in_time) 
                                VALUES (:first_name, :last_name, :company, :reason, :email_contact, :passnumber, :sign_in_time)");
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':company', $company);
        $stmt->bindParam(':reason', $reason);
        $stmt->bindParam(':email_contact', $email_contact);
        $stmt->bindParam(':passnumber', $passnumber);
        $stmt->bindParam(':sign_in_time', $sign_in_time);

        $stmt->execute();

        // Save the inserted ID and type to session
        $_SESSION['guest_id'] = $conn->lastInsertId();
        $_SESSION['sign_in_type'] = 'guest';

        header("Location: ../html files/PolicyInfo.html");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
