<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
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
    $title = $conn->real_escape_string($_POST['title']);
    $location = $conn->real_escape_string($_POST['location']);
    $price = $_POST['price'];
    $bedrooms = intval($_POST['bedrooms']);
    $status = "pending"; // አዲስ ሲገባ በአድሚን እስኪጸድቅ pending ይሆናል

    $uploadedImages = [];
    if (isset($_FILES['images'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $fileName = basename($_FILES['images']['name'][$key]);
            if (!empty($fileName)) {
                $targetFilePath = $targetDir . time() . "_" . $fileName;
                if (move_uploaded_file($tmp_name, $targetFilePath)) {
                    $uploadedImages[] = basename($targetFilePath);
                }
            }
        }
    }

    $imageStr = implode(',', $uploadedImages);

    $sql = "INSERT INTO houses (title, location, price, bedrooms, images, status) VALUES ('$title', '$location', '$price', '$bedrooms', '$imageStr', '$status')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Property published successfully and waiting for admin approval!"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
}
$conn->close();
?>