<?php
include_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $verifica = mysqli_query($conn, "SELECT id FROM verificacao_utilizadores WHERE id = $id");
    if (mysqli_num_rows($verifica) === 0) {
        echo "Conta não encontrada.";
        exit;
    }

    $apagar = mysqli_query($conn, "DELETE FROM verificacao_utilizadores WHERE id = $id");

    if ($apagar) {
        echo "ok";
    } else {
        echo "Erro ao apagar a conta.";
    }
} else {
    echo "Requisição inválida.";
}
?>
