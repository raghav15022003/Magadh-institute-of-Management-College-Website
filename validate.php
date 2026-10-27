<?php


// Database connection
$conn = new mysqli('127.0.0.1:3306', 'u788187284_mpi', 'Magadh@4263', 'u788187284_mpi');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// User input lena
$studentName = $_POST['studentName'];
$fatherName = $_POST['fatherName'];

// Database me check karna
$sql = "SELECT * FROM mpi WHERE studentName='$studentName' OR fatherName='$fatherName'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "Student name or father name is exists"; 
} else {
    echo "not_exists"; 
}

$conn->close();
?>