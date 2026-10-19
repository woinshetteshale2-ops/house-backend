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

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // የገባውን ፓስወርድ በዳታቤዝ ውስጥ ካለው ጋር ማረጋገጥ
        if (password_verify($password, $row['password'])) {
            echo json_encode([
                "status" => "success",
                "user" => [
                    "id" => $row['id'],
                    "name" => $row['name'],
                    "email" => $row['email'],
                    "role" => $row['role']
                ]
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Incorrect Password!"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "User not found!"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Please enter email and password"]);
}
$conn->close();
?>