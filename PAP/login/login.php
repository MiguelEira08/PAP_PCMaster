<?php
session_start();
require_once __DIR__ . '/../db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$erro = '';
$nome_digitado = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome']);
    $password = $_POST['password'];
    $nome_digitado = htmlspecialchars($nome);

    if ($nome === '' || $password === '') {
        $erro = 'Preencha todos os campos!';
    } else {

        // 1️⃣ Procurar utilizador
        $stmt = $conn->prepare("
            SELECT id, nome, password, tipo 
            FROM utilizadores 
            WHERE nome = ?
        ");
        $stmt->bind_param("s", $nome);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $erro = 'Utilizador não encontrado!';
            $stmt->close();
        } else {

            $stmt->bind_result($id, $nome_bd, $hash, $tipo);
            $stmt->fetch();
            $stmt->close();

            // 2️⃣ Garantir registo em utilizador_seguranca
            $conn->query("
                INSERT IGNORE INTO utilizador_seguranca (utilizador_id)
                VALUES ($id)
            ");

            // 3️⃣ Buscar estado de segurança
            $sec = $conn->prepare("
                SELECT tentativas, bloqueado
                FROM utilizador_seguranca
                WHERE utilizador_id = ?
            ");
            $sec->bind_param("i", $id);
            $sec->execute();
            $sec->bind_result($tentativas, $bloqueado);
            $sec->fetch();
            $sec->close();

            // 🚫 4️⃣ BLOQUEIO ABSOLUTO
            if ($bloqueado === 'sim') {
                $erro = 'Conta bloqueada. Contacte um administrador.';
            } else {

                // 5️⃣ Verificar password
                if (password_verify($password, $hash)) {

                    // Login OK → reset tentativas
                    $upd = $conn->prepare("
                        UPDATE utilizador_seguranca
                        SET tentativas = 0,
                            ultimo_login = NOW()
                        WHERE utilizador_id = ?
                    ");
                    $upd->bind_param("i", $id);
                    $upd->execute();
                    $upd->close();

                    $_SESSION['user_id'] = $id;
                    $_SESSION['nome'] = $nome_bd;
                    $_SESSION['tipo'] = $tipo;

                    header('Location: ../index/index.php');
                    exit();

                } else {

                    // ❌ Password errada
                    $tentativas++;

                    if ($tentativas >= 5) {
                        // Bloqueio definitivo
                        $upd = $conn->prepare("
                            UPDATE utilizador_seguranca
                            SET tentativas = ?, bloqueado = 'sim'
                            WHERE utilizador_id = ?
                        ");
                        $upd->bind_param("ii", $tentativas, $id);
                        $upd->execute();
                        $upd->close();

                        $erro = 'Conta bloqueada por excesso de tentativas.';
                    } else {
                        $upd = $conn->prepare("
                            UPDATE utilizador_seguranca
                            SET tentativas = ?
                            WHERE utilizador_id = ?
                        ");
                        $upd->bind_param("ii", $tentativas, $id);
                        $upd->execute();
                        $upd->close();

                        $erro = 'Senha incorreta! Tentativas restantes: ' . (5 - $tentativas);
                    }
                }
            }
        }
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
            <input type="text" name="nome" value="<?= $nome_digitado ?>" required><br>

            <label class="amiko-semibold">Password:</label>
            <input type="password" name="password" required><br>

            <button type="submit">Entrar</button>
            <br>
            <a href="recuperarpasse.php" class="amiko-semibold">Esqueceu-se da palavra-passe?</a><br><br>
            <p class="amiko-semibold">Ainda não tem conta? <a href="registar.php">Registar</a></p>
            <p><a href="../index/index.php">Voltar</a></p>
        </form>
    </div>
</div>
</body>
</html>
