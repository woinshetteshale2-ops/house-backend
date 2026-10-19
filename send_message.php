<?php

// PHP ኤረሮች/ማስጠንቀቂያዎች JSON መልስን እንዳያበላሹ ማድረግ
error_reporting(0);
ini_set('display_errors', 0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");


header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");


$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "house_rental";
$port = 3307;

$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_name = $conn->real_escape_string($_POST['sender_name']);
    $sender_email = $conn->real_escape_string($_POST['sender_email']);
    $sender_role = $conn->real_escape_string($_POST['sender_role']);
    $message = $conn->real_escape_string($_POST['message']);

    $sql = "INSERT INTO messages (sender_name, sender_email, sender_role, message) VALUES ('$sender_name', '$sender_email', '$sender_role', '$message')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Message sent to Admin successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
}
$conn->close();
?>