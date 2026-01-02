<?php
session_start();
include_once __DIR__ . '/../db.php';
include_once __DIR__ . '/../cabecindex.php';
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../index/index.php");
    exit;
}

require_once '../PHPMailer/PHPMailer.php';
require_once '../PHPMailer/SMTP.php';
require_once '../PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$erro    = '';
$sucesso = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $erro = 'ID inválido.';
} else {
    $id = (int) $_GET['id'];

    // Buscar utilizador + estado
    $stmt = $conn->prepare("
        SELECT u.*, us.bloqueado
        FROM utilizadores u
        LEFT JOIN utilizador_seguranca us
            ON u.id = us.utilizador_id
        WHERE u.id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $erro = 'Utilizador não encontrado.';
    } else {
        $utilizador = $result->fetch_assoc();
        $estadoAnterior = $utilizador['bloqueado'] ?? 'nao';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $nome       = trim($_POST['nome']);
            $email      = trim($_POST['email']);
            $numtel     = trim($_POST['numtel']);
            $senhaNova  = trim($_POST['password']);
            $tipo       = $_POST['tipo'];
            $bloqueado  = $_POST['bloqueado']; // sim | nao

            if (empty($nome) || empty($email) || empty($numtel) || empty($tipo)) {
                $erro = 'Nome, e-mail, telefone e tipo são obrigatórios.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erro = 'E-mail inválido.';
            } elseif (!preg_match('/^\d{9}$/', $numtel)) {
                $erro = 'Telefone inválido.';
            } elseif (!in_array($bloqueado, ['sim', 'nao'])) {
                $erro = 'Estado inválido.';
            } else {

                // Atualizar utilizador
                if ($senhaNova !== '') {
                    $hash = password_hash($senhaNova, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("
                        UPDATE utilizadores
                           SET nome = ?, email = ?, numtel = ?, password = ?, tipo = ?
                         WHERE id = ?
                    ");
                    $stmt->bind_param("sssssi", $nome, $email, $numtel, $hash, $tipo, $id);
                } else {
                    $stmt = $conn->prepare("
                        UPDATE utilizadores
                           SET nome = ?, email = ?, numtel = ?, tipo = ?
                         WHERE id = ?
                    ");
                    $stmt->bind_param("ssssi", $nome, $email, $numtel, $tipo, $id);
                }

                if ($stmt->execute()) {

                    // Atualizar estado de segurança
                    $stmtSeg = $conn->prepare("
                        INSERT INTO utilizador_seguranca (utilizador_id, bloqueado)
                        VALUES (?, ?)
                        ON DUPLICATE KEY UPDATE bloqueado = ?
                    ");
                    $stmtSeg->bind_param("iss", $id, $bloqueado, $bloqueado);
                    $stmtSeg->execute();
                    $stmtSeg->close();

                    // 👉 SE O ESTADO MUDOU → ENVIAR EMAIL
                    if ($estadoAnterior !== $bloqueado) {

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
                            $mail->addAddress($email, $nome);
                            $mail->isHTML(true);
                            
                            if ($bloqueado === 'sim') {
                                $mail->Subject = 'Conta Bloqueada';
                                $mail->Body = "
                                    <p>Olá <strong>{$nome}</strong>,</p>
                                    <p>A sua conta foi <strong style='color:red;'>bloqueada</strong> por um administrador.</p>
                                    <p>Se acha que se trata de um erro, entre em contacto com o suporte.</p>
                                ";
                            } else {
                                $mail->Subject = 'Conta Desbloqueada';
                                $mail->Body = "
                                    <p>Olá <strong>{$nome}</strong>,</p>
                                    <p>A sua conta foi <strong style='color:green;'>desbloqueada</strong>.</p>
                                    <p>Já pode voltar a aceder normalmente.</p>
                                ";
                            }

                            $mail->send();
                        } catch (Exception $e) {
                            error_log('Erro ao enviar email de estado: ' . $mail->ErrorInfo);
                        }
                    }

                    $sucesso = 'Utilizador atualizado com sucesso!';

                    $utilizador['nome']      = $nome;
                    $utilizador['email']     = $email;
                    $utilizador['numtel']    = $numtel;
                    $utilizador['tipo']      = $tipo;
                    $utilizador['bloqueado'] = $bloqueado;

                } else {
                    $erro = 'Erro ao atualizar utilizador.';
                }
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Utilizador</title>
    <link rel="stylesheet" href="../css/admin_criar.css">
</head>
<body>

<div class="bg">
 <div class="overlay"></div>
 <div class="content">

  <form method="POST">
    <h2>Editar Utilizador</h2>

    <?php if ($erro): ?>
        <p class="error-message"><?= htmlspecialchars($erro) ?></p>
    <?php elseif ($sucesso): ?>
        <p class="success-message"><?= htmlspecialchars($sucesso) ?></p>
    <?php endif; ?>

    <?php if (isset($utilizador)): ?>

        <label>Nome:</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($utilizador['nome']) ?>" required>

        <label>E-mail:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($utilizador['email']) ?>" required>

        <label>Telefone (9 dígitos):</label>
        <input type="text" name="numtel" value="<?= htmlspecialchars($utilizador['numtel']) ?>"
               required pattern="\d{9}" title="Introduza 9 dígitos">

        <label>Nova password:<br><small>(Deixe em branco para manter a atual)</small></label>
        <input type="password" name="password" minlength="6">

        <label>Tipo:</label>
        <select name="tipo" required>
            <option value="utilizador" <?= $utilizador['tipo'] === 'utilizador' ? 'selected' : '' ?>>Utilizador</option>
            <option value="admin" <?= $utilizador['tipo'] === 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>

        <label>Estado da Conta:</label>
        <select name="bloqueado" required>
            <option value="nao" <?= ($utilizador['bloqueado'] ?? 'nao') === 'nao' ? 'selected' : '' ?>>Ativa</option>
            <option value="sim" <?= ($utilizador['bloqueado'] ?? 'nao') === 'sim' ? 'selected' : '' ?>>Bloqueada</option>
        </select>
            <br><br>
           <div align="center"><button type="submit" class="botao">Guardar alterações</button></div> 
            <br>
        <div align="center"><button type="button" class="botao2" onclick="window.location.href='../admin/admin_utilizadores.php';">Voltar</button></div>

    <?php endif; ?>

  </form>

 </div>
</div>

</body>
</html>
