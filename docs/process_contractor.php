<!--To use PHP Script, need to create a MySQL database named contractor_db and create a table named contractors with the following structure (in sql):

CREATE TABLE contractors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    company VARCHAR(100) NOT NULL,
    service_provided VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    sign_in_date DATETIME NOT NULL
);

-->

<?php
// Database connection parameters
$servername = "localhost";
$username = "your_username"; // Replace with your MySQL username
$password = "your_password"; // Replace with your MySQL password
$dbname = "contractor_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form data when submitted
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Sanitize inputs
    $first_name = filter_input(INPUT_GET, 'first-name', FILTER_SANITIZE_STRING);
    $last_name = filter_input(INPUT_GET, 'last-name', FILTER_SANITIZE_STRING);
    $company = filter_input(INPUT_GET, 'company', FILTER_SANITIZE_STRING);
    $service = filter_input(INPUT_GET, 'service', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_GET, 'contact', FILTER_SANITIZE_EMAIL);
    
    // Prepare SQL statement
    $sql = "INSERT INTO contractors (first_name, last_name, company, service_provided, email, sign_in_date)
            VALUES (?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        // Bind parameters
        $stmt->bind_param("sssss", $first_name, $last_name, $company, $service, $email);
        
        // Execute statement
        if ($stmt->execute()) {
            // Redirect to success page or next form
            header("Location: ./html files/SignInEndScreen.html");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}

// Close connection
$conn->close();
?>