<?php
require_once '../db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../PHPMailer/PHPMailer.php';
require_once '../PHPMailer/SMTP.php';
require_once '../PHPMailer/Exception.php';

$email = isset($_GET['email']) ? trim($_GET['email']) : '';

if (!$email) {
    die("Pedido inválido.");
}

// Verificar se a conta ainda existe
$stmt = $conn->prepare("
    SELECT nome FROM verificacao_utilizadores WHERE email = ?
");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("Conta não encontrada ou já verificada.");
}

// Gerar novo código
$novo_codigo = random_int(100000, 999999);

// Atualizar código e tempo
$stmt = $conn->prepare("
    UPDATE verificacao_utilizadores
    SET codigo_verificacao = ?, duracao = NOW()
    WHERE email = ?
");
$stmt->bind_param("ss", $novo_codigo, $email);
$stmt->execute();
$stmt->close();

/* =====================
   ENVIAR EMAIL
===================== */

$mail = new PHPMailer(true);

try {
    $mail->CharSet = 'UTF-8';
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'pcmastergeral@gmail.com';
    $mail->Password = 'mjsv oxar shbz dfzp';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('pcmastergeral@gmail.com', 'PcMaster');
    $mail->addAddress($email, $user['nome']);

    $mail->isHTML(true);
    $mail->Subject = 'Novo código de verificação - PcMaster';

    $mail->Body = "
        <h2>Olá, {$user['nome']}!</h2>
        <p>Foi solicitado um novo código de verificação.</p>
        <h1 style='letter-spacing:5px;'>$novo_codigo</h1>
        <p>Este código é válido por 10 minutos.</p>
    ";

    $mail->send();

} catch (Exception $e) {
    die("Erro ao enviar email.");
}

// Voltar para a página de verificação
header("Location: verificar_codigo.php?email=" . urlencode($email));
exit();
?>