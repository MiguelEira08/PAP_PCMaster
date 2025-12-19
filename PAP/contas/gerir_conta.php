<?php
session_start();
include_once __DIR__ . '/../db.php';
include_once __DIR__ . '/../cabecindex.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../PHPMailer/Exception.php';
require_once '../PHPMailer/PHPMailer.php';
require_once '../PHPMailer/SMTP.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit();
}

$id = $_SESSION['user_id'];
$erro = '';
$sucesso = '';

$stmt = $conn->prepare("SELECT nome, email, numtel FROM utilizadores WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$utilizador = $result->fetch_assoc();
$stmt->close();

if (!$utilizador) {
    $erro = 'Utilizador não encontrado!';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $numtel = trim($_POST['numtel']);
    $senhaNova = trim($_POST['password']);

    if (empty($nome) || empty($email) || empty($numtel)) {
        $erro = 'Todos os campos exceto a password são obrigatórios!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail inválido!';
    } elseif (!preg_match('/^\d{9}$/', $numtel)) {
        $erro = 'Telefone inválido (9 dígitos)!';
    } else {
        $senhaEnviada = '';
        if ($senhaNova !== '') {
            $hash = password_hash($senhaNova, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("
                UPDATE utilizadores
                SET nome = ?, email = ?, numtel = ?, password = ?
                WHERE id = ?
            ");
            $stmt->bind_param("ssssi", $nome, $email, $numtel, $hash, $id);
            $senhaEnviada = $senhaNova;
        } else {
            $stmt = $conn->prepare("
                UPDATE utilizadores
                SET nome = ?, email = ?, numtel = ?
                WHERE id = ?
            ");
            $stmt->bind_param("sssi", $nome, $email, $numtel, $id);
        }

        if ($stmt->execute()) {
            $sucesso = 'Conta atualizada com sucesso!';
            $utilizador['nome'] = $nome;
            $utilizador['email'] = $email;
            $utilizador['numtel'] = $numtel;
            $stmt->close();

            // ENVIAR EMAIL COM AS CREDENCIAIS ATUALIZADAS
            $mail = new PHPMailer(true);

            try {
                $mail->CharSet = 'UTF-8';
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com'; // Ex: smtp.gmail.com
                $mail->SMTPAuth   = true;
                $mail->Username   = 'pcmastergeral@gmail.com';
                $mail->Password   = 'mjsv oxar shbz dfzp';
                $mail->SMTPSecure = 'tls'; // ou 'ssl'
                $mail->Port       = 587; // ou 465

                $mail->setFrom('pcmastergeral@gmail.com', 'PcMaster');
                $mail->addAddress($email, $nome);

                $mail->isHTML(true);
                $mail->Subject = 'Credênciais atualizadas';

                $body = "<h2>Olá, <strong>$nome!</strong></h2>
                    <p>As suas credênciais foram atualizados com sucesso. Segue um resumo:</p>
                    <ul>
                        <li><strong>Nome:</strong> $nome</li>
                        <li><strong>Email:</strong> $email</li>";
                if ($senhaEnviada !== '') {
                    $body .= "<li><strong>Nova Password:</strong> $senhaEnviada</li>";
                }else{
                    $body .= "<li>Não alterou a sua password, então não a compartilharemos</li>";
                }
                $body .= "</ul><p>Se não realizou esta alteração, contacte-nos imediatamente. <br>PcMaster</p>";

                $mail->Body = $body;

                $mail->send();
            } catch (Exception $e) {
                $erro = "Conta atualizada, mas falha ao enviar email: {$mail->ErrorInfo}";
            }

        } else {
            $erro = 'Erro ao atualizar: ' . $stmt->error;
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Conta</title>
    <link rel="stylesheet" href="../css/conta.css">
</head>
<body>
<div class="bg">
    <div class="overlay"></div>
    <div class="content">
        <h2 style="color: white;">Editar Conta</h2>

        <?php if ($erro): ?>
            <p style="color: red;"><?= htmlspecialchars($erro) ?></p>
        <?php elseif ($sucesso): ?>
            <p style="color: green;"><?= htmlspecialchars($sucesso) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Nome:</label>
            <input type="text" name="nome" value="<?= htmlspecialchars($utilizador['nome']) ?>" required>

            <label>E-mail:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($utilizador['email']) ?>" required>

            <label>Telefone (9 dígitos):</label>
            <input type="text" name="numtel" value="<?= htmlspecialchars($utilizador['numtel']) ?>" pattern="\d{9}" required>

            <label>Nova Password (opcional):</label>
            <input type="password" name="password" minlength="6">

            <button type="submit">Guardar Alterações</button>
            <br>
            <a href="conta.php" class="btn voltar" style="margin-left: 10px;">Voltar</a>
        </form>
    </div>
</div>
</body>
</html>
