<?php
include '../../config.php';
require_once '../../../vendor/autoload.php';

session_start();

$conn = new mysqli($dbhost, $dbuser, $dbpass, $db);

$body = json_decode(file_get_contents('php://input'), true);
$login = $body['login'];
$otp_code = $body['otpCode'];

$sql = "SELECT 2fa_secret, username FROM users WHERE login = '$login'";
$result = mysqli_query($conn, $sql);

$ga = new PHPGangsta_GoogleAuthenticator();
$user = mysqli_fetch_assoc($result);
$secret = $user['2fa_secret'];
$tolerance = 2;
$checkResult = $ga->verifyCode($secret, $otp_code, $tolerance);

if ($checkResult) {
  header('Content-Type: application/json');
  echo json_encode(['isCorrect' => true, 'username' => $user['username']]);
} else {
  header('Content-Type: application/json');
  echo json_encode(['isCorrect' => false, 'message' => 'OTP code is incorrect']);
}