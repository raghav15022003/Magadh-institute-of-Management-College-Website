<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'vendor/autoload.php'; // PHPSpreadsheet library
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


//servername = "localhost";
//$username = "u788187284_MPI_Admission"; // Change if different
//$password = "Magadh@4263";      // Change if different
//$dbname = "u788187284_MPI_Admission";

// Create connection
//$conn = new mysqli($servername, $username, $password, $dbname);




$conn = new mysqli('localhost', 'root', '', 'your_database');

// Fetch form data
$query = "SELECT * FROM form_data";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Add headers
    $sheet->setCellValue('A1', 'Roll Number');
    $sheet->setCellValue('B1', 'Branch Name');
    $sheet->setCellValue('C1', 'Session');
    $sheet->setCellValue('D1', 'Consultant Name');
    $sheet->setCellValue('E1', 'Student Name');
    $sheet->setCellValue('F1', 'Father Name');
    $sheet->setCellValue('G1', 'Mother Name');
    $sheet->setCellValue('H1', 'Date of Birth');
    $sheet->setCellValue('I1', 'Gender');
    $sheet->setCellValue('J1', 'Address');
    $sheet->setCellValue('K1', 'Photo');
    $sheet->setCellValue('L1', 'UTR Number');
    $sheet->setCellValue('M1', 'QR Code');
    $sheet->setCellValue('N1', 'Created At');

    // Add data
    $row = 2;
    while ($data = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $row, $data['roll_number']);
        $sheet->setCellValue('B' . $row, $data['branch_name']);
        $sheet->setCellValue('C' . $row, $data['session']);
        $sheet->setCellValue('D' . $row, $data['consultant_name']);
        $sheet->setCellValue('E' . $row, $data['student_name']);
        $sheet->setCellValue('F' . $row, $data['father_name']);
        $sheet->setCellValue('G' . $row, $data['mother_name']);
        $sheet->setCellValue('H' . $row, $data['dob']);
        $sheet->setCellValue('I' . $row, $data['gender']);
        $sheet->setCellValue('J' . $row, $data['address']);
        $sheet->setCellValue('K' . $row, $data['photo']);
        $sheet->setCellValue('L' . $row, $data['utr_number']);
        $sheet->setCellValue('M' . $row, $data['qr_code']);
        $sheet->setCellValue('N' . $row, $data['created_at']);
        $row++;
    }

    // Download as Excel file
    $writer = new Xlsx($spreadsheet);
    $fileName = 'form_data_' . date('Y-m-d_H-i-s') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$fileName\"");
    $writer->save('php://output');
    exit;
} else {
    echo "No data found!";
}
?>
