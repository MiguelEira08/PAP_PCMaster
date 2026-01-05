<?php
session_start();
include_once __DIR__ . '/../db.php';

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'admin') {
    echo 'erro';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'erro';
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    echo 'erro';
    exit;
}

$stmt = $conn->prepare("DELETE FROM menu WHERE id_menu = ?");

if ($stmt) {
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo 'ok';
    } else {
        echo 'erro';
    }

    $stmt->close();
} else {
    echo 'erro';
}
