<?php
include '../../config.php';
$conn = new mysqli($dbhost, $dbuser, $dbpass, $db);
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"] === true) {
  $data = json_decode(file_get_contents('php://input'), true);
  $id = $data['id'];

  $sql = "DELETE FROM prizes WHERE id = $id";
  $conn->query($sql);

  header('Content-Type: application/json');
  echo json_encode(['success' => true]);
}