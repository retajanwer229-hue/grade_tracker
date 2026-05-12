<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require 'db.php';
echo json_encode(["session" => session_id(), "user_id" => $_SESSION["user_id"] ?? "not set", "db" => "connected"]);
?>