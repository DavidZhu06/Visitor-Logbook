<?php
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
        $IDCI_Contact = $_POST['IDCI_Contact'];
        $email_contact = $_POST['email_contact'];
        $sign_in_time = date('Y-m-d H:i:s');
    
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO interview (first_name, last_name, IDCI_Contact, email_contact, sign_in_time) VALUES (:first_name, :last_name, :IDCI_Contact, :email_contact, :sign_in_time)");
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':IDCI_Contact', $IDCI_Contact);
        $stmt->bindParam(':email_contact', $email_contact);
        $stmt->bindParam(':sign_in_time', $sign_in_time);

        // Execute the statement
        $stmt->execute();

        // Redirect to PolicyInfo.html on success
        header("Location: ./html files/PolicyInfo.html");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>