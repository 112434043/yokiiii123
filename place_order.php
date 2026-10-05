<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "campus_bites";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(["status" => "error", "message" => "Invalid data"]);
    exit;
}

$student_name = $conn->real_escape_string($input['studentName']);
$student_id   = $conn->real_escape_string($input['studentId']);
$department   = $conn->real_escape_string($input['department']);
$phone        = $conn->real_escape_string($input['phone']);
$pickup_time  = $conn->real_escape_string($input['pickupTime']);
$total_amount = floatval($input['totalAmount']);
$items_summary = $conn->real_escape_string(json_encode($input['items']));

$sql = "INSERT INTO orders (student_name, student_id, department, phone, pickup_time, items_summary, total_amount) 
        VALUES ('$student_name', '$student_id', '$department', '$phone', '$pickup_time', '$items_summary', '$total_amount')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["status" => "success", "order_id" => $conn->insert_id]);
} else {
    echo json_encode(["status" => "error", "message" => $conn->error]);
}

$conn->close();
?>