<?php
ob_start();
session_start();
include_once __DIR__ . '/../db.php'; // Usando db.php como no seu modelo
include_once __DIR__ . '/../cabecindex.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../PHPMailer/Exception.php';
require_once '../PHPMailer/PHPMailer.php';
require_once '../PHPMailer/SMTP.php';

// Verificação de segurança: No seu modelo a sessão é 'user_id'
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit();
}

$id_utilizador = $_SESSION['user_id'];
$erro = '';
$sucesso = '';

// Buscar dados do utilizador para o e-mail
$stmt = $conn->prepare("SELECT nome, email FROM utilizadores WHERE id = ?");
$stmt->bind_param("i", $id_utilizador);
$stmt->execute();
$utilizador = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data_agendamento = $_POST['data_agendamento'] ?? '';
    $tipo_servico = $_POST['tipo_servico'] ?? '';
    $localidade = trim($_POST['localidade'] ?? '');

    if (empty($data_agendamento) || empty($tipo_servico) || empty($localidade)) {
        $erro = 'Todos os campos são obrigatórios!';
    } else {
        // 1. Inserir na base de dados
        $stmt = $conn->prepare("INSERT INTO agendamentos (utilizador_id, data_agendamento, tipo_servico, localidade) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $id_utilizador, $data_agendamento, $tipo_servico, $localidade);

        if ($stmt->execute()) {
            $sucesso = 'Agendamento realizado com sucesso! Voltando em 1 segundo...';
            
            // 2. Enviar e-mail de notificação (PHPMailer)
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

                $mail->setFrom('pcmastergeral@gmail.com', 'PcMaster Agendamentos');
                $mail->addAddress('migueleira08@gmail.com', 'Administrador');
                // Pode adicionar mais administradores aqui
                
                $mail->isHTML(true);
                $mail->Subject = 'Novo Agendamento - ' . ucfirst($tipo_servico);
                $body = "
                    <h3>Novo pedido de agendamento</h3>
                    <p><strong>Utilizador:</strong> {$utilizador['nome']} ({$utilizador['email']})</p>
                    <p><strong>Serviço:</strong> " . ucfirst($tipo_servico) . "</p>
                    <p><strong>Data/Hora:</strong> " . date('d/m/Y H:i', strtotime($data_agendamento)) . "</p>
                    <p><strong>Localidade:</strong> $localidade</p>
                ";

                $mail->Body = $body;
                $mail->send();
                $redir = true;
            } catch (Exception $e) {
                // Mesmo que o email falhe, o agendamento foi salvo
                $redir = true; 
            }
        } else {
            $erro = 'Erro ao processar o agendamento na base de dados.';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="../imagens/icon.png">
    <title>Marcar Agendamento</title>
    <link rel="stylesheet" href="../css/conta.css">
</head>
<body>
    <a href="javascript:history.back()" class="botao-voltar voltar-fixo">← Voltar</a>

<div class="bg">
    <div class="overlay"></div>

    <div class="content">
        <form method="POST" class="form-conta">
            <?php if ($erro): ?>
                <p class="error-message"><?= htmlspecialchars($erro) ?></p>
            <?php elseif ($sucesso): ?>
                <p class="success-message"><?= htmlspecialchars($sucesso) ?></p>
                <?php if (!empty($redir)): ?>
                    <script>
                        setTimeout(function () {
                            window.location.href = 'saber_mais.php';
                        }, 1000);
                    </script>
                <?php endif; ?>
            <?php endif; ?>

            <center>
                <h2 align="center">Marcar Agendamento</h2>
                <p style="color: white;">Preencha os dados para solicitar um serviço</p>
                <br>
            </center>

            <label>Data e Hora pretendida</label>
            <input type="datetime-local" name="data_agendamento" required>
            <br>

            <label>Tipo de Serviço</label>
            <select name="tipo_servico" required style="width: 100%; padding: 10px; border-radius: 5px; margin-top: 5px;">
                <option value="">Selecione uma opção...</option>
                <option value="montagem">Montagem de Equipamento</option>
                <option value="reparação">Reparação / Manutenção</option>
            </select>
            <br><br>

            <label>Localidade</label>
            <input type="text" name="localidade" placeholder="Ex: Rua da Saudade, 123, Aveiro" required>
            <br>

            <center>
                <br>
                <button type="submit" class="botao">Confirmar Marcação</button><br>
            </center>
        </form>
    </div>
</div>

</body>
</html>