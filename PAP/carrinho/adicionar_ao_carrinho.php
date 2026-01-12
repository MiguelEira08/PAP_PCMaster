<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['user_id'])) {

    $_SESSION['redirect_after_login'] = $_SERVER['HTTP_REFERER'] ?? '../index/index.php';

    header("Location: ../login/login.php");
    exit;
}

$id_utilizador = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_produto = isset($_POST['id_produto']) ? (int)$_POST['id_produto'] : 0;
    $tipo_produto = $_POST['tipo_produto'] ?? '';
    $quantidade = isset($_POST['quantidade']) ? (int)$_POST['quantidade'] : 1;
    $acao = $_POST['acao'] ?? 'carrinho';

    if (!in_array($tipo_produto, ['componente', 'periferico'])) {
        die("Tipo de produto inválido.");
    }

    $stmt = $conn->prepare("SELECT id, quantidade FROM carrinho WHERE tipo_produto = ? AND id_produto = ? AND id_utilizador = ?");
    $stmt->bind_param("sii", $tipo_produto, $id_produto, $id_utilizador);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($linha = $result->fetch_assoc()) {
        $nova_quantidade = $linha['quantidade'] + $quantidade;
        $update = $conn->prepare("UPDATE carrinho SET quantidade = ? WHERE id = ?");
        $update->bind_param("ii", $nova_quantidade, $linha['id']);
        $update->execute();
        $update->close();
    } else {
        $insert = $conn->prepare("INSERT INTO carrinho (tipo_produto, id_produto, quantidade, id_utilizador) VALUES (?, ?, ?, ?)");
        $insert->bind_param("siii", $tipo_produto, $id_produto, $quantidade, $id_utilizador);
        $insert->execute();
        $insert->close();
    }

    $stmt->close();

    if ($acao === "comprar") {
        header("Location: ../carrinho/carrinho.php");
    } else {
        $referer = $_SERVER['HTTP_REFERER'];
        $delimiter = strpos($referer, '?') !== false ? '&' : '?';
        header("Location: " . $referer . $delimiter . "sucesso=1");
    }
}
?>
