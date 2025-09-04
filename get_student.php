<?php
header("Content-Type: application/json");
$host = "localhost"; $user = "root"; $pass = ""; $db = "barcode_db";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { echo json_encode(["status"=>"error"]); exit(); }

$code = $_GET['code'] ?? '';
$res = $conn->query("SELECT * FROM students WHERE code_text='".$conn->real_escape_string($code)."'");
if ($row = $res->fetch_assoc()) {
    echo json_encode(["status"=>"success","name"=>$row['name']]);
} else {
    echo json_encode(["status"=>"error","message"=>"Student not found"]);
}
$conn->close();
