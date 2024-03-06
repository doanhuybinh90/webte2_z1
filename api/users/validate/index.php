<?php
include '../../config.php';

$conn = new mysqli($dbhost, $dbuser, $dbpass, $db);


$body = json_decode(file_get_contents('php://input'), true);
$email = $body['email'];

$sql = "SELECT * FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  header('Content-Type: application/json');
  echo json_encode(['exists' => true]);
} else {
  header('Content-Type: application/json');
  echo json_encode(['exists' => false]);
}