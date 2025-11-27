<?php
include_once 'cabeconta.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../db.php';
require_once '../PHPMailer/PHPMailer.php';
require_once '../PHPMailer/SMTP.php';
require_once '../PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit;
}

$utilizador_id = (int) $_SESSION['user_id'];
$mensagem_sucesso = '';
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $motivo = trim($_POST['Motivo'] ?? '');
    $mensagem = trim($_POST['feedback'] ?? '');
    $origem = trim($_POST['origem_pagina'] ?? '');
    $status = 'por ler';
    $data_envio = date("Y-m-d H:i:s");

    if ($motivo === '' || $mensagem === '') {
        $erros[] = 'Motivo e mensagem são obrigatórios.';
    }

    if (empty($erros)) {
        // Obter nome do utilizador
        $stmtNome = $conn->prepare("SELECT nome FROM utilizadores WHERE id = ?");
        $stmtNome->bind_param("i", $utilizador_id);
        $stmtNome->execute();
        $resultNome = $stmtNome->get_result();
        $nomeUtilizador = $resultNome->fetch_assoc()['nome'] ?? 'Utilizador Desconhecido';
        $stmtNome->close();

        // Inserir feedback no BD
        $stmt = $conn->prepare("
            INSERT INTO feedback (user_id, Motivo, feedback, origem_pagina, data_envio, status)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        if (!$stmt) {
            $erros[] = "Erro ao preparar statement: " . $conn->error;
        } else {
            $stmt->bind_param("isssss", $utilizador_id, $motivo, $mensagem, $origem, $data_envio, $status);
            $stmt->execute();
            $stmt->close();
            $mensagem_sucesso = "Feedback enviado com sucesso!";

            // Enviar email ao admin
            $mail = new PHPMailer(true);
            try {
                $mail->CharSet = 'UTF-8';
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'pcmastergeral@gmail.com';
                $mail->Password   = 'kvej gmhk njdd mqqy';
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom('pcmastergeral@gmail.com', 'PcMaster');
                $mail->addAddress('migueleira08@gmail.com', 'Miguel'); // Substitui por email real do admin
                $mail->isHTML(true);
                $mail->Subject = "Novo Feedback Recebido";

                $mail->Body = "
                    <p><strong>Nome do utilizador:</strong> {$nomeUtilizador}</p>
                    <p><strong>Origem do erro:</strong> " . ($origem ?: 'Não especificado') . "</p>
                    <p><strong>Motivo do erro:</strong> {$motivo}</p>
                    <p><strong>Mensagem do cliente:</strong> {$mensagem}</p>
                    <p><strong>Data do feedback:</strong> {$data_envio}</p>
                ";
                $mail->send();
            } catch (Exception $e) {
                error_log("Erro ao enviar email para o admin: " . $mail->ErrorInfo);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Enviar Feedback</title>
    <link rel="stylesheet" href="../css/conta.css">
</head>
<body>

<div class="bg">
    <div class="overlay"></div>
    <div class="content">

        <h2 style="color: white;">Enviar Feedback</h2>

        <form method="POST" action="">
        <?php if (!empty($mensagem_sucesso)): ?>
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
            <label for="Motivo">Motivo do Feedback:</label><br>
            <input type="text" name="Motivo" id="Motivo" maxlength="250" required><br><br>

            <label for="origem_pagina">Origem (página ou funcionalidade):</label><br>
            <input type="text" name="origem_pagina" id="origem_pagina" maxlength="250"><br><br>

            <label for="feedback">Mensagem:</label><br>
            <input type="text" name="feedback" id="feedback" maxlength="100"><br><br>

           <div align="center"><button type="submit" class="botao">Enviar Feedback</button></div> 
            <br>
        <div align="center"><button type="button" class="botao2" onclick="window.history.back()">Voltar</button></div>
            </div>
        </form>

    </div>
</div>

</body>
</html>
