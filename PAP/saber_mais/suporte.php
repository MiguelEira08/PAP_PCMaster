<?php
include_once __DIR__ . '/../cabecindex.php';
require_once '../PHPMailer/PHPMailer.php';
require_once '../PHPMailer/SMTP.php';
require_once '../PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mensagem_sucesso = '';
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $motivo = trim($_POST['motivo'] ?? '');
    $mensagem = trim($_POST['mensagem'] ?? '');
    $data_envio = date("Y-m-d H:i:s");

    if ($nome === '' ||$email === '' || $motivo === '' || $mensagem === '') {
        $erros[] = 'Todos os campos são obrigatórios.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'Email inválido.';
    }

    if (empty($erros)) {
        $mail = new PHPMailer(true);

        try {
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'pcmastergeral@gmail.com';
            $mail->Password   = 'mjsv oxar shbz dfzp';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('pcmastergeral@gmail.com', 'PcMaster');
            $mail->addAddress('migueleira08@gmail.com', 'Administrador');
            $mail->addAddress('al.919786@aeaav.pt', 'Administrador');

            $mail->isHTML(true);
            $mail->Subject = 'Suporte ao Cliente - Novo Pedido';

            $mail->Body = "
            <p>O seguinnte utilzador enviou um pedido de suporte:</p><br>
                <p><strong>Nome do utilizador:</strong> {$nome}</p>
                <p><strong>Email do utilizador:</strong> {$email}</p>
                <p><strong>Motivo do contacto:</strong> {$motivo}</p>
                <p><strong>Mensagem:</strong></p>
                <p>{$mensagem}</p>
                <p><strong>Data:</strong> {$data_envio}</p>
            ";

            $mail->send();
            $mensagem_sucesso = 'Pedido de suporte enviado com sucesso!';
        } catch (Exception $e) {
            $erros[] = 'Erro ao enviar o pedido de suporte.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Suporte</title>
    <link rel="stylesheet" href="../css/conta.css">
</head>
<body>

<div class="bg">
    <div class="overlay"></div>
    <div class="content">

        <h2 style="color: white;">Contactar Suporte</h2>

        <form method="POST">

            <?php if ($mensagem_sucesso): ?>
                <p class="success-message"><?= htmlspecialchars($mensagem_sucesso) ?></p>
            <?php endif; ?>

            <?php if ($erros): ?>
                <ul class="error-message">
                    <?php foreach ($erros as $erro): ?>
                        <li><?= htmlspecialchars($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <div style="text-align: center;">
                <label>Nome de Utilizador:</label><br>
                <input type="nome" name="nome" maxlength="250" required><br><br>

                <label>Email:</label><br>
                <input type="email" name="email" maxlength="250" required><br><br>

                <label>Motivo do contacto:</label><br>
                <input type="text" name="motivo" maxlength="250" required><br><br>

                <label>Explicação:</label><br>
                <input type="text" name="mensagem" maxlength="500" required><br><br>

                <div align="center">
                    <button type="submit" class="botao">Enviar Pedido</button>
                </div>
                <br>
                <div align="center">
                    <button type="button" class="botao2"><a href="saber_mais.php">Voltar</a></button>
                </div>
            </div>
        </form>

    </div>
</div>

</body>
</html>
