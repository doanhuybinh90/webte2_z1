<?php

require_once '../../../vendor/autoload.php';

session_start(); 

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $ga = new PHPGangsta_GoogleAuthenticator();
    if (!isset($_SESSION['secret'])) {
        $user_secret = $ga->createSecret();
        $_SESSION['secret'] = $user_secret;
    } else {
        $user_secret = $_SESSION['secret'];
    }
    $qrCodeUrl = $ga->getQRCodeGoogleUrl('webte1_dzehtsiarou', $user_secret);
    header('Content-Type: application/json');
    echo json_encode(['qrCodeUrl' => $qrCodeUrl]);
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ga = new PHPGangsta_GoogleAuthenticator();
    $secret = $_SESSION['secret'];
    $oneCode = json_decode(file_get_contents('php://input'), true)['oneCode'];
    $result = $ga->verifyCode($secret, $oneCode, 2);
    header('Content-Type: application/json');
    echo json_encode(['verified' => $result]);
}
