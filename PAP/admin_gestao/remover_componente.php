<?php
session_start();
include_once '../db.php';
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../index/index.php");
    exit;
}

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo 'erro';
    exit;
}


$id = intval($_POST['id']);

$stmt = $conn->prepare("SELECT caminho_arquivo FROM componentes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo 'erro';
    exit;
}

$row = $result->fetch_assoc();
$caminhoImagem = '../imagens/' . $row['caminho_arquivo'];
$stmt->close();

$stmt = $conn->prepare("DELETE FROM componentes WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if (file_exists($caminhoImagem)) {
        unlink($caminhoImagem);
    }

    echo 'ok';
} else {
    echo 'erro';
}

$stmt->close();
$conn->close();
?>
