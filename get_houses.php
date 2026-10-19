<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
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

$sql = "SELECT * FROM houses ORDER BY id DESC";
$result = $conn->query($sql);

$houses = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $houses[] = $row;
    }
}

echo json_encode($houses);
$conn->close();
?>