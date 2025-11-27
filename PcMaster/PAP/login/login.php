<?php
session_start();
require_once __DIR__ . '/../db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $password = trim($_POST['password']);

    if (empty($nome) || empty($password)) {
        $erro = 'Preencha todos os campos!';
    } else {
        // Busca o utilizador (admin ou cliente)
        $stmt = $conn->prepare("SELECT id, nome, password, tipo FROM utilizadores WHERE nome = ?");
        $stmt->bind_param("s", $nome);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $nome_bd, $senha_bd, $tipo);
            $stmt->fetch();

            if (password_verify($password, $senha_bd)) {
                $_SESSION['nome'] = $nome_bd;
                $_SESSION['user_id'] = $id;
                $_SESSION['tipo'] = $tipo;

                // Redireciona conforme o tipo
                if ($tipo === 'admin') {
                    header('Location: ../admin/admin_dashboard.php');
                } else {
                    header('Location: ../index.php');
                }
                exit();
            } else {
                $erro = 'Senha incorreta!';
            }
        } else {
            $erro = 'Utilizador não encontrado!';
        }

        $stmt->close();
    }
    $conn->close();
}
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
        <form method="POST" action="">
            <h2 class="amiko-semibold">Login</h2>
            <img src="../imagens/logo.png" height="200px" width="200px">

            <?php if ($erro): ?>
                <p style="color: red;"> <?= htmlspecialchars($erro) ?> </p>
            <?php endif; ?>

            <label class="amiko-semibold">Nome de Utilizador</label>
            <input type="text" name="nome" required><br>

            <label class="amiko-semibold">Password:</label>
            <input type="password" name="password" required><br>

            <button type="submit">Entrar</button>
            <br>
            <a href="recuperarpasse.php" class="amiko-semibold">Esqueceu-se da palavra-passe?</a><br><br>
            <p class="amiko-semibold">Ainda não tem conta? <a href="registar.php">Registar</a></p>
        </form>
    </div>
</div>
</body>
</html>
