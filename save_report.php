<?php
$host = "localhost"; $user = "root"; $pass = ""; $db = "barcode_db";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("DB connection failed");

$code = $_POST['studentCode'] ?? '';
$misb = $_POST['misbehaviour'] ?? '';

if ($code && $misb) {
    $res = $conn->query("SELECT id FROM students WHERE code_text='".$conn->real_escape_string($code)."'");
    if ($row = $res->fetch_assoc()) {
        $student_id = $row['id'];
        $conn->query("INSERT INTO reports (student_id, misbehaviour) VALUES ($student_id, '".$conn->real_escape_string($misb)."')");
        echo "<script>alert('Report saved!'); window.location='scanner.html';</script>";
    } else {
        echo "<script>alert('Student not found!'); window.location='scanner.html';</script>";
    }
} else {
    echo "<script>alert('Invalid input!'); window.location='scanner.html';</script>";
}
$conn->close();
