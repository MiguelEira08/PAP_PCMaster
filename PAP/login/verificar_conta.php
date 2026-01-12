<?php
session_start();
require_once '../db.php';

$erro = '';
$mensagem = '';
$email = isset($_GET['email']) ? trim($_GET['email']) : '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (!$email) {
        $erro = "Pedido inválido.";
    } else {

        $stmt = $conn->prepare("SELECT * FROM verificacao_utilizadores WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (!$user) {
            $erro = "Este link é inválido ou a conta já foi eliminada.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT * FROM verificacao_utilizadores WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        $erro = "A conta já expirou ou foi eliminada.";
    } else {

        $stmt = $conn->prepare("
            INSERT INTO utilizadores (nome, email, numtel, password, caminho_arquivo, tipo)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "ssssss",
            $user['nome'],
            $user['email'],
            $user['numtel'],
            $user['password'],
            $user['caminho_arquivo'],
            $user['tipo']
        );
        $stmt->execute();
        $stmt->close();

        $del = $conn->prepare("DELETE FROM verificacao_utilizadores WHERE email = ?");
        $del->bind_param("s", $email);
        $del->execute();
        $del->close();

        $mensagem = "Conta verificada com sucesso! A redirecionar para o login...";
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Verificar Conta</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
<div class="bg">
    <div class="overlay">
        <center>
            <form method="POST">

                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">

                <h2>Verificar Conta</h2>
                <img src="../imagens/logo.png" height="200px">
                <?php if ($erro): ?>
                    <p style="color:red; font-weight:bold;"><?= $erro ?></p>
                <?php endif; ?>

                <?php if ($mensagem): ?>
                    <p style="color:green; font-weight:bold;"><?= $mensagem ?></p>
                    <script>
                        setTimeout(() => {
                            window.location.href = "login.php";
                        }, 3000);
                    </script>
                <?php endif; ?>

                <?php if (!$erro && !$mensagem): ?>
                    <p>Conta encontrada! Clique abaixo para verificar.</p>
                    <br>
                    <button type="submit">Verificar Conta</button>
                <?php endif; ?>

            </form>
        </center>
    </div>
</div>
</body>
</html>
