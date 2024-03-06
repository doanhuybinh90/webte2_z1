<?php
include '../../config.php';

session_start();

$conn = new mysqli($dbhost, $dbuser, $dbpass, $db);

$body = json_decode(file_get_contents('php://input'), true);
echo json_encode($body);
$username = $body['username'];
$password = $body['password'];
$email = $body['email'];
$secret = $_SESSION['secret'];
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (username, email, login, password_hash, 2fa_secret) VALUES ('$username', '$email', '$email', '$hashedPassword', '$secret')";
$result = mysqli_query($conn, $sql);

if ($result) {
  session_unset();
  header('Content-Type: application/json');
  echo json_encode(['success' => true]);
} else {
  header('Content-Type: application/json');
  echo json_encode(['success' => false]);
}