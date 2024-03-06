<?php

session_start();

if(isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"] === true) {
  header('Content-Type: application/json');
  echo json_encode(['isAuthorized' => true, 'username' => $_SESSION["username"], 'email' => $_SESSION["email"]]);
} else {
  header('Content-Type: application/json');
  echo json_encode(['isAuthorized' => false]);
}