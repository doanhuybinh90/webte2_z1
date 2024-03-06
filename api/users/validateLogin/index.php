<?php
include '../../config.php';

session_start();

$conn = new mysqli($dbhost, $dbuser, $dbpass, $db);

$body = json_decode(file_get_contents('php://input'), true);
$login = $body['login'];

$sql = "SELECT * FROM users WHERE email = '$login'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) < 1) {
  header('Content-Type: application/json');
  echo json_encode(['isCorrect' => false, 'message' => 'User not found']);
  exit;
}

$password = $body['password'];
$user = mysqli_fetch_assoc($result);
if (password_verify($password, $user['password_hash'])) {
  $_SESSION["loggedIn"] = true;
  $_SESSION["username"] = $user['username'];
  $_SESSION["email"] = $user['email'];

  header('Content-Type: application/json');
  echo json_encode(['isCorrect' => true]);
} else {
  header('Content-Type: application/json');
  echo json_encode(['isCorrect' => false, 'message' => 'Password is incorrect']);
}