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
    echo json_encode(["status" => "error", "message" => "Connection failed"]);
    exit();
}

if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['role'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // ለደህንነት ፓስወርዱን Hash ለማድረግ
    $role = $conn->real_escape_string($_POST['role']);

    // ኢሜይሉ አስቀድሞ መኖሩን ማረጋገጥ
    $checkUser = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($checkUser);

    if ($result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email already exists!"]);
    } else {
        $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["status" => "success", "message" => "Registration successful! You can now login."]);
        } else {
            echo json_encode(["status" => "error", "message" => $conn->error]);
        }
    }
} else {
    echo json_encode(["status" => "error", "message" => "Please fill all fields!"]);
}
$conn->close();
?>