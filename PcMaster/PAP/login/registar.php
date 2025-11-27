<?php
session_start();
require_once '../db.php';

// PHPMailer sem Composer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../PHPMailer/PHPMailer.php';
require_once '../PHPMailer/SMTP.php';
require_once '../PHPMailer/Exception.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $nome = trim($_POST['username']);
    $numtel = trim($_POST['telefone']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($email) || empty($nome) || empty($password) || empty($confirm_password) || empty($numtel)) {
        $erro = 'Preencha todos os campos!';
    } elseif (!preg_match('/^\d{9}$/', $numtel)) {
        $erro = 'O número de telemóvel deve conter exatamente 9 dígitos.';
    } elseif ($password !== $confirm_password) {
        $erro = 'As passwords não coincidem!';
    } else {
        $stmt = $conn->prepare("SELECT id FROM utilizadores WHERE email = ? OR nome = ?");
        $stmt->bind_param("ss", $email, $nome);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $erro = 'Email ou utilizador já existe!';
        } else {
            $stmt->close();

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO utilizadores (nome, email, numtel, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nome, $email, $numtel, $hashed_password);

            if ($stmt->execute()) {
                $_SESSION['nome'] = $nome;
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['role'] = 'cliente';

                // Enviar email de boas-vindas
                $mail = new PHPMailer(true);

                try {
                    $mail->CharSet = 'UTF-8';
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'pcmastergeral@gmail.com'; // teu email
                    $mail->Password   = 'kvej gmhk njdd mqqy'; // senha ou senha de aplicação
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = 587;

                    $mail->setFrom('pcmastergeral@gmail.com', 'PcMaster');
                    $mail->addAddress($email, $nome);

                    $mail->isHTML(true);
                    $mail->Subject = 'Bem-vindo á PcMaster!';
                    $mail->Body    = "
                        <h2>Olá, $nome!</h2>
                        <p>Obrigado por se registar no nosso site.</p>
                        <p><strong>As suas credenciais:</strong></p>
                        <ul>
                            <li><strong>Nome:</strong> $nome</li>
                            <li><strong>Email:</strong> $email</li>
                            <li>Não podemos fornecer a password, caso se tenha esquecido, contacte-nos em pcmastergeral@gmail.com</li>
                        </ul>
                        <p>Estamos felizes por tê-lo connosco!<br><strong>PcMaster</strong></p>
                    ";

                    $mail->send();
                    // Email enviado com sucesso
                } catch (Exception $e) {
                    error_log("Erro ao enviar email: {$mail->ErrorInfo}");
                }

                header('Location: login.php');
                exit();
            } else {
                $erro = 'Erro ao registar!';
            }
            $stmt->close();
        }
    }
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
    <title>Registar</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
<div class="bg">
    <div class="overlay">
    <center>

        <form method="POST" action="">
            <h2 class="amiko-semibold">Registar</h2>
            <?php if ($erro): ?>
                <p style="color: red;"> <?= htmlspecialchars($erro) ?> </p>
            <?php endif; ?>
                <br>
            <label class="amiko-semibold">Nome de utilizador:</label>
            <input type="text" name="username" required><br>

            <label class="amiko-semibold">Email:</label>
            <input type="email" name="email" required><br>

            <label class="amiko-semibold">Telefone:</label>
            <input type="text" name="telefone" pattern="\d{9}" maxlength="9" required title="Digite exatamente 9 números."><br>

            <label class="amiko-semibold">Password:</label>
            <input type="password" name="password" required minlength="6"><br>

            <label class="amiko-semibold">Confirmar Password:</label>
            <input type="password" name="confirm_password" required minlength="6"><br><br>

            <button type="submit">Registar</button>
            <br>
            <p>Já tem conta? <a href="login.php">Iniciar Sessão</a></p>
        </form>
    </center>
    </div>
</div>
</body>
</html>
