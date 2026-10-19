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
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM houses WHERE id = $id";

    if($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "House deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
}
$conn->close();
?>