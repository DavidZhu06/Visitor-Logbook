<?php
require __DIR__ . '/../vendor/autoload.php'; 
use PhpOffice\PhpSpreadsheet\Spreadsheet; //for shorter, cleaner class names
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// --- Email Notification with PHPMailer ---
// Load PHPMailer classes manually (no autoloading)
/*
require './php files/PHPMailer.php';
require './php files/SMTP.php';
require './php files/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmail($today, $filepath) { 
    $mailConfig = require __DIR__ . '/mail_config.php';
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = $mailConfig['smtp_server'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $mailConfig['smtp_username'];
        $mail->Password   = $mailConfig['smtp_password'];
        $mail->SMTPSecure = 'tls';
        $mail->Port       = $mailConfig['smtp_port'];

        $mail->setFrom('mailsystem@idci.ca', 'Imperial Distributors Canada Inc.');
        $mail->addAddress('Camille.Ngtiong@idci.ca', 'Camille');

        $mail->isHTML(true);
        $mail->Subject = "Export Ready - {$today}";
        $mail->Body    = "Hello,<br><br>The Visitor Logbook export for <strong>{$today}</strong> is ready.<br><br>File location: <code>{$filepath}</code><br><br>Regards,<br>Export System";
        $mail->AltBody = "Hello,\n\nThe Visitor Logbook export for {$today} is ready.\n\nFile location: {$filepath}\n\nRegards,\nExport System";

        $mail->send();
        echo "Email notification sent successfully.";
    } catch (Exception $e) {
        echo "Email could not be sent. PHPMailer Error: {$mail->ErrorInfo}";
    }
}
*/

// Load DB config
$config = require __DIR__ . '/config.php';
$env = 'production';
$dbConfig = $config[$env];

try {
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset=utf8mb4",
        $dbConfig['username'],
        $dbConfig['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Set timezone
    date_default_timezone_set('America/Vancouver');

    // Prepare filename
    $today = date('Y-m-d');
    $filename = "visitorlogbook_export_{$today}.xlsx";
    $filepath = "\\\\BC-FS.idci.local\\Company\\VisitorLog\\{$filename}";

    // Tables to export
    $tables = ['contractors', 'deliveries', 'guests', 'interviews'];

    // Create Spreadsheet
    $spreadsheet = new Spreadsheet(); //without the use lines at the top, you would need to use full class name - $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $spreadsheet->removeSheetByIndex(0); // Remove default sheet

    foreach ($tables as $index => $table) {
        $stmt = $pdo->query("SELECT * FROM `$table`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sheet = new Worksheet($spreadsheet, ucfirst($table));
        $spreadsheet->addSheet($sheet, $index);

        if (!empty($rows)) {
            $headers = array_keys($rows[0]);
            $sheet->fromArray([$headers], null, 'A1');
            $sheet->fromArray($rows, null, 'A2');
        } else {
            $sheet->setCellValue('A1', 'No data found');
        }
    }

    // Save file
    $writer = new Xlsx($spreadsheet);
    $writer->save($filepath);

    echo "Export successful: $filename";

    // Call the function after export
    // sendEmail($today, $filepath);

} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
} catch (Exception $e) {
    echo "Export Error: " . $e->getMessage();
}
