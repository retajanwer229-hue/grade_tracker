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
    $subject    = trim($_POST["subject"] ?? '');
    $assessment = trim($_POST["assessment"] ?? '');
    $mark       = $_POST["mark"] ?? '';
    $total      = $_POST["total"] ?? '';

    // Validation
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

    $stmt = $conn->prepare("INSERT INTO grades (user_id, subject, assessment, mark, total) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$_SESSION["user_id"], $subject, $assessment, $mark, $total])) {
        echo json_encode(["success" => true, "message" => "Grade added successfully."]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error adding grade."]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed."]);
}
?>
