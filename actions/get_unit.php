<?php
require_once '../config/db.php';

try {
    $stmt = $pdo->query("SELECT * FROM unit");

    $units = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'message' => json_encode($units)]);
    die;


} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

?>