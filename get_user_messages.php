<?php
// CORS Header - ከማንኛውም Origin የሚመጡ ጥያቄዎችን ለመፍቀድ
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// የ OPTIONS ጥያቄ ከመጣ ወዲያውኑ ምላሽ ሰጥቶ እንዲወጣ
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// PHP ኤረሮች JSON እንዳያበላሹ ማድረግ
error_reporting(0);
ini_set('display_errors', 0);

header("Content-Type: application/json; charset=UTF-8");

// ... የቀረው የ PHP ዳታቤዝ ኮድህ እዚህ ይ ቀጥላል ...


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

if (isset($_GET['email'])) {
    $email = $conn->real_escape_string($_GET['email']);
  $sql = "SELECT * FROM messages WHERE sender_email='$email' ORDER BY id DESC";
    $result = $conn->query($sql);

    $messages = array();
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    echo json_encode($messages);
} else {
    echo json_encode([]);
}

$conn->close();
?>