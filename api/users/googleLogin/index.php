<?php
include '../../config.php';
session_start();
$conn = new mysqli($dbhost, $dbuser, $dbpass, $db);
if($_SERVER['REQUEST_METHOD'] === 'POST') {

  $data = json_decode(file_get_contents('php://input'), true);
  $email = $data['email'];
  $username = $data['username'];

  // Check if user exists
  $sql = "SELECT * FROM users WHERE email = '$email'";
  $result = mysqli_query($conn, $sql);
  if(mysqli_num_rows($result) > 0) {
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;
    $_SESSION['loggedIn'] = true;
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    return;
  }

  $sql = "INSERT INTO users (username, email, login, password_hash, 2fa_secret) VALUES ('$username', '$email', '$email', '', '')";
  $result = mysqli_query($conn, $sql);
  $_SESSION['username'] = $username;
  $_SESSION['email'] = $email;
  $_SESSION['loggedin'] = true;

  header('Content-Type: application/json');
  echo json_encode(['success' => true]);
}