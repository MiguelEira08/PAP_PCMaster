<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['user_id'])) {
    die("Precisas de iniciar sessão.");
}

$id_utilizador = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['carrinho_id'])) {
    $carrinho_id = (int) $_POST['carrinho_id'];

    $stmt = $conn->prepare("DELETE FROM carrinho WHERE id = ? AND id_utilizador = ?");
    $stmt->bind_param("ii", $carrinho_id, $id_utilizador);
    $stmt->execute();
    $stmt->close();
}

header("Location: carrinho.php");
exit;