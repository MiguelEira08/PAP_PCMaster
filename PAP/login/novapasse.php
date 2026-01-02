<?php
session_start();
require_once __DIR__ . '/../db.php';
require '../phpmailer/PHPMailer.php';
require '../phpmailer/SMTP.php';
require '../phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

error_reporting(E_ALL);
ini_set('display_errors', 1);

$mensagem = '';
$erro = '';
$erro_email = '';
$email_pref = '';

// Verifica se o email foi passado na URL
if (isset($_GET['email'])) {
    $email_pref = trim($_GET['email']);

    // Verifica se o email existe na base de dados
    $stmt = $conn->prepare("SELECT id FROM utilizadores WHERE email = ?");
    $stmt->bind_param("s", $email_pref);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $erro_email = 'Este email não está associado a nenhuma conta!';
        $email_pref = ''; // limpa se não estiver na base
    }

    $stmt->close();
}

// Processa o POST do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $nova_pass = trim($_POST['nova_pass']);
    $confirmar_pass = trim($_POST['confirmar_pass']);

    if (empty($email) || empty($nova_pass) || empty($confirmar_pass)) {
        $erro = 'Preencha todos os campos!';
    } elseif ($nova_pass !== $confirmar_pass) {
        $erro = 'As palavras-passe não coincidem!';
    } else {
        // Verifica novamente se o email existe
        $stmt = $conn->prepare("SELECT id FROM utilizadores WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $nova_hash = password_hash($nova_pass, PASSWORD_DEFAULT);

            $stmt_update = $conn->prepare("UPDATE utilizadores SET password = ? WHERE email = ?");
            $stmt_update->bind_param("ss", $nova_hash, $email);

            if ($stmt_update->execute()) {
                try {
                    // Envia email de confirmação
                    $mail = new PHPMailer(true);
                    $mail->CharSet = 'UTF-8';
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'pcmastergeral@gmail.com'; 
                    $mail->Password   = 'mjsv oxar shbz dfzp'; 
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = 587;

                    $mail->setFrom('pcmastergeral@gmail.com', 'PcMaster');
                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = 'Alteração de Palavra-Passe';
                    $mail->Body    = '<strong>A sua palavra-passe foi alterada com sucesso.</strong><br>
                                      Se não foi você, por favor contacte o suporte imediatamente.';

                    $mail->send();
                } catch (Exception $e) {
                    // Opcional: guardar erro de envio
                }

                $mensagem = 'Palavra-passe alterada com sucesso! A redirecionar para o login...';
            } else {
                $erro = 'Erro ao atualizar a palavra-passe. Tente novamente.';
            }

            $stmt_update->close();
        } else {
            $erro = 'Este email não está associado a nenhuma conta!';
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Palavra-Passe</title>
    <link href="https://fonts.googleapis.com/css2?family=Amiko:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
<div class="bg">
    <div class="overlay">
        <center>
            <form method="POST" action="">
                <h2 class="amiko-semibold">Repor Palavra-Passe</h2>
                <img src="../imagens/logo.png" height="200px" width="200px" alt="Logo">

                <?php if ($erro_email): ?>
                    <p style="color: #ff4d4d; font-weight: bold;"> <?= htmlspecialchars($erro_email) ?> </p>
                <?php endif; ?>

                <?php if ($erro): ?>
                    <p style="color: #ff4d4d; font-weight: bold;"> <?= htmlspecialchars($erro) ?> </p>
                <?php endif; ?>

                <?php if ($mensagem): ?>
                    <p style="color: #000000; font-weight: bold;"> <?= htmlspecialchars($mensagem) ?> </p>
                    <script>
                        setTimeout(function() {
                            window.location.href = "../login/login.php";
                        }, 3000);
                    </script>
                <?php endif; ?>

                <?php if (!$mensagem && !$erro_email): ?>
                    <label class="amiko-semibold">Email</label>
                    <input type="email" name="email" required value="<?= htmlspecialchars($email_pref) ?>" readonly><br>

                    <label class="amiko-semibold">Nova Palavra-Passe</label>
                    <input type="password" name="nova_pass" required><br>

                    <label class="amiko-semibold">Confirmar Palavra-Passe</label>
                    <input type="password" name="confirmar_pass" required><br>

                    <button type="submit">Alterar Palavra-Passe</button>
                    <br><br>
                    <p class="amiko-semibold"><a href="login.php">Voltar ao Login</a></p>
                <?php endif; ?>
            </form>
        </center>
    </div>
</div>
</body>
</html>
