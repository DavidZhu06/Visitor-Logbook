<?php

session_start(); // <-- Needed in order to store data in $_SESSION 
/*PHP sessions allow for storing user information to be used across multiple pages of a web application. Sesson variables (specific pieces of data stored in a session for a single user) hold information about one single user, and are available to all pages in the same application, and by default last until the user closes the browser (in which they are auto cleared). Each user gets their own session so data is isolated and not shared with users. */

// Load config file (database credentials)
$config = require __DIR__ . '/../config.php'; // __DIR__ returns the absolute path of the directory where the current php script file resides

$env = 'production'; // Sets variable which indicates the environment that you are currently running the code in. Change this to 'production' when deploying

$dbConfig = $config[$env]; // Get the correct config - selects which database credentials to use based on the environment (production or staging)

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
        $company = filter_input(INPUT_POST, 'company', FILTER_DEFAULT) ? htmlspecialchars(trim($_POST['company']), ENT_QUOTES, 'UTF-8') : '';
        $custom_company = filter_input(INPUT_POST, 'custom_company', FILTER_DEFAULT) ? htmlspecialchars(trim($_POST['custom_company']), ENT_QUOTES, 'UTF-8') : '';
        $email_contact = filter_input(INPUT_POST, 'contact', FILTER_DEFAULT) ? htmlspecialchars(trim($_POST['contact']), ENT_QUOTES, 'UTF-8') : '';
        $service = filter_input(INPUT_POST, 'service', FILTER_DEFAULT) ? htmlspecialchars(trim($_POST['service']), ENT_QUOTES, 'UTF-8') : '';
        $custom_service = filter_input(INPUT_POST, 'custom_service', FILTER_DEFAULT) ? htmlspecialchars(trim($_POST['custom_service']), ENT_QUOTES, 'UTF-8') : '';
        $passnumber = filter_input(INPUT_POST, 'passnumber', FILTER_DEFAULT) ? htmlspecialchars(trim($_POST['passnumber']), ENT_QUOTES, 'UTF-8') : '';
        $sign_in_time = date('Y-m-d H:i:s');

        /* Can also use the following if you prefer not to use filter_input:
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $company = $_POST['company'];
        $service = $_POST['service'];
        $email_contact = $_POST['contact'];
        $passnumber = $_POST['passnumber'];
        $sign_in_time = date('Y-m-d H:i:s');
        */
        
        // Determine the company name to store
        $company_name = $company;
        if ($company === "Other") {
            if (empty($custom_company)) {
                throw new Exception("Please enter a company name for 'Other'.");
            }
            $company_name = $custom_company;
        }

        // Determine the service name to store
        $service_provided = $service;
        if ($service === "Other") {
            if (empty($custom_service)) {
                throw new Exception("Please enter a company name for 'Other'.");
            }
            $service_provided = $custom_service;
        }
    
        // Prepare and execute SQL statement
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

