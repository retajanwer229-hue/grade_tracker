
<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$stmt = $conn->prepare("SELECT id, subject, assessment, mark, total FROM grades WHERE user_id = ? ORDER BY subject ASC");
$stmt->execute([$_SESSION["user_id"]]);
$grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Cast numeric fields to proper types
$grades = array_map(function($g) {
    return [
        'id'         => (int)   $g['id'],
        'subject'    =>         $g['subject'],
        'assessment' =>         $g['assessment'],
        'mark'       => (float) $g['mark'],
        'total'      => (float) $g['total'],
    ];
}, $grades);

echo json_encode($grades);
?>
