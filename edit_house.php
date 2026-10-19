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
    $id = intval($_POST['id']);
    $title = $conn->real_escape_string($_POST['title']);
    $location = $conn->real_escape_string($_POST['location']);
    $price = $_POST['price'];
    $bedrooms = intval($_POST['bedrooms']);

    // ፎቶዎች አዲስ ከገቡ ማስተካከል
    $imageQueryPart = "";
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $targetDir = "uploads/";
        $uploadedImages = [];
        
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $fileName = basename($_FILES['images']['name'][$key]);
            if (!empty($fileName)) {
                $targetFilePath = $targetDir . time() . "_" . $fileName;
                if (move_uploaded_file($tmp_name, $targetFilePath)) {
                    $uploadedImages[] = basename($targetFilePath);
                }
            }
        }
        
        if (!empty($uploadedImages)) {
            $imageStr = implode(',', $uploadedImages);
            $imageQueryPart = ", images='$imageStr'";
        }
    }

    $sql = "UPDATE houses SET title='$title', location='$location', price='$price', bedrooms='$bedrooms' $imageQueryPart, status='pending' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Property and images updated successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
}
$conn->close();
?>