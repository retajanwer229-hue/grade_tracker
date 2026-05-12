<?php
require 'db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$stmt = $conn->prepare("
    SELECT subject,
           ROUND(AVG(mark / total * 100), 2) AS avg_percent,
           COUNT(*) AS count
    FROM grades
    WHERE user_id = ?
    GROUP BY subject
    ORDER BY subject ASC
");
$stmt->execute([$_SESSION["user_id"]]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$data = array_map(function($row) {
    return [
        'subject'     => $row['subject'],
        'avg_percent' => (float) $row['avg_percent'],
        'count'       => (int)   $row['count'],
    ];
}, $data);

echo json_encode($data);
?>
