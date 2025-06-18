<?php
// Set timezone to Pacific Time
date_default_timezone_set('America/Vancouver');

// Database connection
$host = "localhost";
$dbname = "visitorlogbook_db";
$username = "root";
$password = "IDCIp@ssDZ2025!";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $first_name = $_POST['first-name'];
        $last_name = $_POST['last-name'];
        $recipient = $_POST['recipient'];
        $sign_in_time = date('Y-m-d H:i:s');

        // Get the selected value from dropdown
        $company_value = $_POST['company'];

        // If user selected "Other", take the custom input
        if ($company_value === "5") {
            $company = $_POST['custom-company'];
        } else {
            $company = companyNameFromValue($company_value);
        }

        // Prepare SQL insert
        $stmt = $conn->prepare("INSERT INTO delivery (first_name, last_name, company, recipient, sign_in_time) 
                                VALUES (:first_name, :last_name, :company, :recipient, :sign_in_time)");
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':company', $company);
        $stmt->bindParam(':recipient', $recipient);
        $stmt->bindParam(':sign_in_time', $sign_in_time);
        $stmt->execute();

        // Redirect on success
        header("Location: ./html files/SignInEndScreen.html");
        exit();
    }
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
}

// Map dropdown value to company name
function companyNameFromValue($value) {
    switch ($value) {
        case "1": return "FedEx";
        case "2": return "UPS";
        case "3": return "Purolator";
        case "4": return "Canada Post";
        default: return "Unknown";
    }
}
?>
