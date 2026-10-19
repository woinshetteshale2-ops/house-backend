<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$conn = new mysqli("127.0.0.1", "root", "", "house_rental", 3307);
$result = $conn->query("SELECT * FROM messages ORDER BY id DESC");
$messages = [];

while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}
echo json_encode($messages);
$conn->close();
?>