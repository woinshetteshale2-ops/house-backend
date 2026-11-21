<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// የዳታቤዝ ግንኙነት - እንደ ፋይልሽ ስም/ቅንብር አስተካክዪ
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

$house_id = isset($_POST['house_id']) ? intval($_POST['house_id']) : 0;
$tenant_name = isset($_POST['tenant_name']) ? trim($_POST['tenant_name']) : '';
$tenant_email = isset($_POST['tenant_email']) ? trim($_POST['tenant_email']) : '';
$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
$payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '';
$transaction_ref = isset($_POST['transaction_ref']) ? trim($_POST['transaction_ref']) : '';

if ($house_id <= 0 || empty($tenant_name) || $amount <= 0 || empty($payment_method) || empty($transaction_ref)) {
    echo json_encode(["success" => false, "message" => "Please fill in all required payment fields."]);
    exit();
}

$receiptPath = null;
if (isset($_FILES['receipt_image']) && $_FILES['receipt_image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . "/uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $cleanName = preg_replace("/[^A-Za-z0-9._-]/", "_", basename($_FILES['receipt_image']['name']));
    $fileName = time() . "_receipt_" . $cleanName;
    $targetFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['receipt_image']['tmp_name'], $targetFile)) {
        $receiptPath = "uploads/" . $fileName;
    }
}

$stmt = $conn->prepare("INSERT INTO payments (house_id, tenant_name, tenant_email, amount, payment_method, transaction_ref, receipt_image, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");
$stmt->bind_param("issdsss", $house_id, $tenant_name, $tenant_email, $amount, $payment_method, $transaction_ref, $receiptPath);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Payment submitted! Waiting for admin verification."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to save payment: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
