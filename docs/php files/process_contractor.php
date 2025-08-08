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
        // Use htmlspecialchars to prevent Cross-Site Scripting attacks (basically making sure that the user input isn't interpreted as JavaScript or HTML) and trim to remove whitespace
        $first_name = htmlspecialchars(trim($_POST['first_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $last_name = htmlspecialchars(trim($_POST['last_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $company = htmlspecialchars(trim($_POST['company'] ?? ''), ENT_QUOTES, 'UTF-8');
        $custom_company = htmlspecialchars(trim($_POST['custom_company'] ?? ''), ENT_QUOTES, 'UTF-8');
        $email_contact = htmlspecialchars(trim($_POST['contact'] ?? ''), ENT_QUOTES, 'UTF-8');
        $service = htmlspecialchars(trim($_POST['service'] ?? ''), ENT_QUOTES, 'UTF-8');
        $custom_service = htmlspecialchars(trim($_POST['custom_service'] ?? ''), ENT_QUOTES, 'UTF-8');
        $passnumber = htmlspecialchars(trim($_POST['passnumber'] ?? ''), ENT_QUOTES, 'UTF-8');
        $sign_in_time = date('Y-m-d H:i:s');

        /* Can also use the following (not as safe):
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
                throw new Exception("Please enter a custom service for 'Other'.");
            }
            $service_provided = $custom_service;
        }
    
        // Prepare and execute SQL statement
        $stmt = $conn->prepare("INSERT INTO contractors (first_name, last_name, company, service, email_contact, passnumber, sign_in_time) VALUES (:first_name, :last_name, :company, :service, :email_contact, :passnumber, :sign_in_time)");
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':company', $company_name);
        $stmt->bindParam(':service', $service_provided);
        $stmt->bindParam(':email_contact', $email_contact);
        $stmt->bindParam(':passnumber', $passnumber);
        $stmt->bindParam(':sign_in_time', $sign_in_time);

        // Execute the statement
        $stmt->execute();

        $_SESSION['guest_id'] = $conn->lastInsertId(); //lastInsertId() is a PDO method that returns the ID of the most recent row you inserted into the database on this connection. Bascially after contractor signs in, this script will insert contractor's info into the contractors table, mySQL will generate a unique ID for this row, and this line will store that ID in the PHP session under the key 'guest_id' so that it can be used later to update the signature data in process_policy.php
        $_SESSION['sign_in_type'] = 'contractor'; //Stores string 'contractor' in the session under the key 'sign_in_type' to indicate the type of user signed in
        
        // Store sign_in_time in session for use in signature filename
        $_SESSION['sign_in_time'] = $sign_in_time;

        // Store for email 
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;
        $_SESSION['email'] = $email_contact; 
        $_SESSION['company'] = $company_name;
        $_SESSION['service'] = $service_provided;
        $_SESSION['email_contact'] = $email_contact;
        $_SESSION['passnumber'] = $passnumber;
        unset($_SESSION['signature_path']); // Clear any previous signature path by clearing the old value from the current session since the PDF email signature was showing a previous signature 

        // Redirect to PolicyInfo.html on success
        header("Location: ../html%20files/PolicyInfo.html");
        exit();
    } 
} catch (PDOException $e) { // Catch any database connection or query errors
    echo "Error: " . $e->getMessage();
} catch (Exception $e) { // Catch any other exceptions that aren't database related
    echo "Error: " . $e->getMessage();
}
?>

