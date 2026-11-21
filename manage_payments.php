<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$host = "127.0.0.1";
$port = 3307;
$user = "root";
$pass = "";
$db   = "house_rental";

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT p.*, h.title AS house_title, h.location AS house_location 
            FROM payments p 
            LEFT JOIN houses h ON p.house_id = h.id 
            ORDER BY p.created_at DESC";
    $result = $conn->query($sql);
    $payments = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $payments[] = $row;
        }
    }
    echo json_encode(["success" => true, "payments" => $payments]);
    $conn->close();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $payment_id = isset($data['payment_id']) ? intval($data['payment_id']) : 0;
    $action = isset($data['action']) ? $data['action'] : ''; // 'Verified' or 'Rejected'

    if ($payment_id <= 0 || !in_array($action, ['Verified', 'Rejected'])) {
        echo json_encode(["success" => false, "message" => "Invalid payment ID or action."]);
        exit();
    }

    $stmt = $conn->prepare("UPDATE payments SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $action, $payment_id);

    if ($stmt->execute()) {
        if ($action === 'Verified') {
            $getHouse = $conn->prepare("SELECT house_id FROM payments WHERE id = ?");
            $getHouse->bind_param("i", $payment_id);
            $getHouse->execute();
            $res = $getHouse->get_result();
            if ($row = $res->fetch_assoc()) {
                $hid = intval($row['house_id']);
                $updHouse = $conn->prepare("UPDATE houses SET status = 'Booked' WHERE id = ?");
                $updHouse->bind_param("i", $hid);
                $updHouse->execute();
                $updHouse->close();
            }
            $getHouse->close();
        }
        echo json_encode(["success" => true, "message" => "Payment marked as " . $action]);
    } else {
        echo json_encode(["success" => false, "message" => "Update failed: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>