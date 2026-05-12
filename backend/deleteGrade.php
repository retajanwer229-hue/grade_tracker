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
    $id = $_POST["id"] ?? '';

    if (!is_numeric($id) || (int)$id <= 0) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid grade ID."]);
        exit();
    }

    // user_id check prevents deleting another user's grades
    $stmt = $conn->prepare("DELETE FROM grades WHERE id=? AND user_id=?");
    if ($stmt->execute([(int)$id, $_SESSION["user_id"]])) {
        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(["error" => "Grade not found or not yours."]);
        } else {
            echo json_encode(["success" => true, "message" => "Grade deleted."]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error deleting grade."]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed."]);
}
?>
