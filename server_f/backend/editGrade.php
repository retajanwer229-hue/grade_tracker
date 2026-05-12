<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id         = $_POST["id"] ?? '';
    $subject    = trim($_POST["subject"] ?? '');
    $assessment = trim($_POST["assessment"] ?? '');
    $mark       = $_POST["mark"] ?? '';
    $total      = $_POST["total"] ?? '';

    if (!is_numeric($id) || (int)$id <= 0) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid grade ID."]);
        exit();
    }

    if (empty($subject) || empty($assessment)) {
        http_response_code(400);
        echo json_encode(["error" => "Subject and assessment are required."]);
        exit();
    }

    if (!is_numeric($mark) || !is_numeric($total)) {
        http_response_code(400);
        echo json_encode(["error" => "Mark and total must be numeric."]);
        exit();
    }

    $mark  = (float) $mark;
    $total = (float) $total;

    if ($total <= 0) {
        http_response_code(400);
        echo json_encode(["error" => "Total must be greater than zero."]);
        exit();
    }

    if ($mark < 0 || $mark > $total) {
        http_response_code(400);
        echo json_encode(["error" => "Mark must be between 0 and total."]);
        exit();
    }

    $stmt = $conn->prepare("UPDATE grades SET subject=?, assessment=?, mark=?, total=? WHERE id=? AND user_id=?");
    if ($stmt->execute([$subject, $assessment, $mark, $total, (int)$id, $_SESSION["user_id"]])) {
        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(["error" => "Grade not found or not yours."]);
        } else {
            echo json_encode(["success" => true, "message" => "Grade updated."]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error updating grade."]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed."]);
}
?>