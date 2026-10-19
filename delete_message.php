<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
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

// 1. መረጃውን ከ JSON body ለማንበብ ይሞክራል
$data = json_decode(file_get_contents("php://input"), true);

// 2. ከ JSON ካላገኘ ከ $_GET/$_POST ያነባል
$id = $data['id'] ?? $_REQUEST['id'] ?? null;

if ($id) {
    $id = intval($id);
    // ከ contact_messages ወደ messages ታብል ተቀይሯል
    $sql = "DELETE FROM messages WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Message deleted successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid message ID"]);
}

$conn->close();
?>