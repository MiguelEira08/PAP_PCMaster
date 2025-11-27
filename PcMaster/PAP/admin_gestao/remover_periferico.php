<?php
include_once '../db.php';

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo 'ID inválido.';
    exit();
}

$id = intval($_POST['id']);

$stmt = $conn->prepare("SELECT caminho_arquivo FROM perifericos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo 'Periférico não encontrado.';
    exit();
}

$row = $result->fetch_assoc();
$caminhoImagem = '../imagens/' . $row['caminho_arquivo'];
$stmt->close();

$stmt = $conn->prepare("DELETE FROM perifericos WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if (file_exists($caminhoImagem)) {
        unlink($caminhoImagem);
    }
    echo 'ok'; 
} else {
    echo 'Erro ao remover periférico.';
}

$stmt->close();
$conn->close();
?>
