<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

error_reporting(0);
ini_set('display_errors', 0);

header("Content-Type: application/json; charset=UTF-8");

$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "house_rental";
$port = 3307;

$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

// የዳታቤዙን መልእክቶች በሙሉ እናምጣና በ React በኩል እናጣራቸዋለን
$sql = "SELECT * FROM messages ORDER BY id DESC";
$result = $conn->query($sql);

$allMessages = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $allMessages[] = $row;
    }
}

http_response_code(200);
echo json_encode($allMessages);

$conn->close();
?>