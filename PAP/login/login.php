<?php
session_start();

// Mensagem de erro vinda do verificar_passe.php
$erro = $_SESSION['erro_login'] ?? '';
unset($_SESSION['erro_login']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiko:wght@600&display=swap" rel="stylesheet">
    <title>Login</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
<div class="bg">
    <div class="overlay">
        <center>
        <form method="POST" action="verificar_passe.php">
            <h2 class="amiko-semibold">Login</h2>
            <img src="../imagens/logo.png" height="200" width="200">

            <?php if ($erro): ?>
                <p style="color:red;"><?= htmlspecialchars($erro) ?></p>
            <?php endif; ?>

            <label class="amiko-semibold">Nome de Utilizador</label>
            <input type="text" name="nome" required><br>

            <label class="amiko-semibold">Password:</label>
            <input type="password" name="password" required><br>

            <button type="submit">Entrar</button>
            <br>
            <a href="recuperarpasse.php" class="amiko-semibold">
                Esqueceu-se da palavra-passe?
            </a><br><br>

            <p class="amiko-semibold">
                Ainda não tem conta? <a href="registar.php">Registar</a>
            </p>
        </form>
    </div>
</div>
</body>
</html>
