<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "house_rental";
$port = 3307;

$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed"]));
}

// የማመልከቻ ቴብል (Table) ከሌለ ራሱ እንዲፈጥር
$tableQuery = "CREATE TABLE IF NOT EXISTS rental_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    house_id INT,
    name VARCHAR(255),
    phone VARCHAR(50),
    move_in_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($tableQuery);

if(isset($_POST['house_id']) && isset($_POST['name']) && isset($_POST['phone']) && isset($_POST['move_in_date'])) {
    $house_id = intval($_POST['house_id']);
    $name = $conn->real_escape_string($_POST['name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $move_in_date = $conn->real_escape_string($_POST['move_in_date']);

    $sql = "INSERT INTO rental_requests (house_id, name, phone, move_in_date) VALUES ($house_id, '$name', '$phone', '$move_in_date')";

    if($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Booking request submitted successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
}
$conn->close();
?>