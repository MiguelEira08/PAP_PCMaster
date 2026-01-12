<?php
session_start();
include_once '../db.php';
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../index/index.php");
    exit;
}

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo 'ID inválido.';
    exit();
}

$id = intval($_POST['id']);

$stmt = $conn->prepare("SELECT id FROM utilizadores WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo 'Utilizador não encontrado.';
    exit();
}
$stmt->close();

$result = $conn->query("SHOW TABLES LIKE 'carrinho'");
if ($result && $result->num_rows > 0) {
    $stmt = $conn->prepare("DELETE FROM carrinho WHERE id_utilizador = ?");
    if (!$stmt) {
        echo 'Erro carrinho: ' . $conn->error;
        exit();
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

$result = $conn->query("SHOW TABLES LIKE 'fim_compra'");
if ($result && $result->num_rows > 0) {
    $stmt = $conn->prepare("DELETE FROM fim_compra WHERE utilizador_id = ?");
    if (!$stmt) {
        echo 'Erro fim_compra: ' . $conn->error;
        exit();
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

$result = $conn->query("SHOW TABLES LIKE 'feedback'");
$ids_feedback = [];

if ($result && $result->num_rows > 0) {
    $stmt = $conn->prepare("SELECT id FROM feedback WHERE user_id = ?");
    if (!$stmt) {
        echo 'Erro SELECT feedback: ' . $conn->error;
        exit();
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $ids_feedback[] = $row['id'];
    }
    $stmt->close();

    if (!empty($ids_feedback)) {
        $result2 = $conn->query("SHOW TABLES LIKE 'resposta_admin'");
        if ($result2 && $result2->num_rows > 0) {
            $placeholders = implode(',', array_fill(0, count($ids_feedback), '?'));
            $types = str_repeat('i', count($ids_feedback));

            $stmt = $conn->prepare("DELETE FROM resposta_admin WHERE id_feedback IN ($placeholders)");
            if (!$stmt) {
                echo 'Erro DELETE resposta_admin: ' . $conn->error;
                exit();
            }
            $stmt->bind_param($types, ...$ids_feedback);
            $stmt->execute();
            $stmt->close();
        }
    }

    $stmt = $conn->prepare("DELETE FROM feedback WHERE user_id = ?");
    if (!$stmt) {
        echo 'Erro DELETE feedback: ' . $conn->error;
        exit();
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

$stmt = $conn->prepare("DELETE FROM utilizadores WHERE id = ?");
if (!$stmt) {
    echo 'Erro DELETE utilizador: ' . $conn->error;
    exit();
}
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo 'ok';  
} else {
    echo 'Erro ao remover utilizador: ' . $stmt->error;
}

$stmt->close();
$conn->close();
?>
