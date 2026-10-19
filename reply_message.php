<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
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

// 1. መጀመሪያ ከ JSON body ለማንበብ እንሞክራለን
$data = json_decode(file_get_contents("php://input"), true);

// 2. ከ JSON ካላገኘ ከ standard $_POST/$_REQUEST ያነባል
$id = $data['id'] ?? $_REQUEST['id'] ?? $_REQUEST['message_id'] ?? null;
$reply = $data['reply'] ?? $_REQUEST['reply'] ?? $_REQUEST['reply_message'] ?? null;

if ($id && $reply) {
    $id = intval($id);
    $reply = $conn->real_escape_string($reply);

    // ወደ messages ታብል ይጽፋል
    $sql = "UPDATE messages SET reply = '$reply' WHERE id = $id";

    if ($conn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "Reply sent successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database update failed: " . $conn->error]);
    }
} else {
    // ምን እንደጎደለ በግልፅ ይነግረናል
    echo json_encode([
        "status" => "error", 
        "message" => "Invalid parameters. Received ID: " . var_export($id, true) . ", Reply: " . var_export($reply, true)
    ]);
}

$conn->close();
?>