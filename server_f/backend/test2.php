<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require 'db.php';
$stmt = $conn->prepare("SELECT id, subject, assessment, mark, total FROM grades WHERE user_id = ? ORDER BY subject ASC");
$stmt->execute([$_SESSION["user_id"] ?? 0]);
$grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($grades);
?>