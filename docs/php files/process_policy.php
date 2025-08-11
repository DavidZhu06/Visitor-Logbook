<?php

session_start(); // Access session variables

// Load configuration
$config = require __DIR__ . '/../config.php';

$env = 'production'; 

$dbConfig = $config[$env]; 

// Database connection
$host = $dbConfig['host'];
$dbname = $dbConfig['dbname'];
$username = $dbConfig['username'];
$password = $dbConfig['password'];

try {
    // Connects to MySQL database using PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get signature data from POST
    if (isset($_POST['signature']) && !empty($_POST['signature'])) { //basically here we're making sure that the form submission has a signature field (exists in the POST request) and it's not empty
        
        // Remove data URL (data:imagepng;base 64) prefix 
        $signature = $_POST['signature'];
        $signature = preg_replace('/^data:image\/(png|jpeg);base64,/', '', $signature);

        // Use session sign in time to create a unique filename
        $sign_in_time = $_SESSION['sign_in_time'] ?? date('Y-m-d H:i:s'); // ?? is a null coalescing operator - basically if the left side exists and isn't null, use it, otherwise use the right side 
        $date = str_replace([':', ' '], ['-', '_'], $sign_in_time); // Replace : and space with - and _ since filenames on windows can't have : or spaces

        // Building a file path in signatures subfolder (basically where the signature image will be saved)
        $file_path = 'signatures/signature_' . $date . '.png'; //use above $date variable (cleaned version of sign_in_time)
        if (!file_exists('signatures')) { //make sure signature folder exists (if not, this will create one)
            mkdir('signatures', 0755, true);
        }
        file_put_contents($file_path, base64_decode($signature)); // base64 decode Converts Base64 string to raw binary data and saves it to file path
        $_SESSION['signature_path'] = $file_path; //Saves location of saved signature 

        // Lookup table based on sign-in type
        $table_map = [ //the values on the right side (keys) are categories that are used internally (in this case $_SESSION['sign_in_type'] values) and the values on the right side are the actual database table names in mySQL
            'interview' => 'interviews',
            'contractor' => 'contractors',
            'delivery' => 'deliveries',
            'guest' => 'guests'
        ];

        $sign_in_type = $_SESSION['sign_in_type'] ?? null; //pull visitor sign in type from session (set in process_contractor.php, process_delivery.php, process_guest.php, process_interview.php)
        $guest_id = $_SESSION['guest_id'] ?? null; //pull visitor ID from session (set in process_contractor.php, process_delivery.php, process_guest.php, process_interview.php)

        if ($sign_in_type && $guest_id && isset($table_map[$sign_in_type])) { //Checks that sign_in_type and guest_id are set and that the sign_in_type exists in the table_map array
            $table = $table_map[$sign_in_type]; //choose correct table

            // IMPORTANT: Column `signature_data` must exist in each table
            $stmt = $pdo->prepare("UPDATE $table SET signature_data = :signature WHERE id = :id"); //Prepare SQL query to update the signature_data column in the correct table in the correct row (where id matches the guest_id)
        
            // Run the query - above code basically means update the signature data column where the row matches the guest_id
            $stmt->execute([
                ':signature' => $signature,
                ':id' => $guest_id
            ]);


            require_once 'send-mail.php';



            // here we are gathering the form data to send in automatic email
            $formData = [
                'first_name' => $_SESSION['first_name'] ?? '',
                'last_name' => $_SESSION['last_name'] ?? '',
                'email_contact' => $_SESSION['email'] ?? '',
                'company' => ($sign_in_type === 'interview') ? 'N/A' : ($_SESSION['company'] ?? ''), // If the sign-in type is interview, company is set to N/A
                'service' => ($sign_in_type === 'interview' || $sign_in_type === 'guest') ? 'N/A' : ($_SESSION['service'] ?? ''), // if the sign in type is interview, service is set to N/A
                //'email_contact' => $_SESSION['contact'] ?? '', //not used rn, might be used in future
                'passnumber' => $_SESSION['passnumber'] ?? '',
                'sign_in_time' => $_SESSION['sign_in_time'] ?? '',
                'IDCI_Contact' => ($_SESSION['sign_in_type'] === 'interview') ? ($_SESSION['contact'] ?? '') : (($_SESSION['sign_in_type'] === 'guest') ? ($_SESSION['IDCI_Contact'] ?? '') : ''),
            ];

            $recipientEmail = $_SESSION['email_contact'] ?? ''; // Get the email address from the session
            $recipientName = trim($formData['first_name'] . ' ' . $formData['last_name']); // Combine first/last name from form 

            if (!sendEmail($recipientEmail, $recipientName, $formData)) {
                error_log("Failed to send email to $recipientEmail at " . date('Y-m-d H:i:s'));
            }

            // Redirect to parking page if all checks are sucessful
            header('Location: ../html files/ParkingPage.html');
            exit;
        } else {
            // Redirect back to policy info if no signature is sent or if sign_in_type or guest_id is not set
            header('Location: ../html files/PolicyInfo.html');
            exit;
        }
    } else {
        // Redirect back to policy info if no signature is provided 
        header('Location: ../html files/PolicyInfo.html');
        exit;
    }
} catch (PDOException $e) {
    // Handle database errors
    http_response_code(500);
    echo "Database error: " . $e->getMessage();
    //header('Location: ../html files/ParkingPage.html'); // Forces redirect to parking page on error
} catch (Exception $e) {
    // Handle other errors
    http_response_code(400);
    echo "Error: " . $e->getMessage();
    //header('Location: ../html files/ParkingPage.html');
}
?>