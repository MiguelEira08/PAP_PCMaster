<?php
include_once 'cabeconta.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT id, feedback, data_envio, status 
    FROM feedback 
    WHERE user_id = ? AND (status IS NULL OR status != 'lida')
    ORDER BY data_envio DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_porler = $stmt->get_result();
$feedbacks_porler = $result_porler->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$stmt = $conn->prepare("
    SELECT f.id, f.feedback, f.data_envio, ra.id AS resposta_id 
    FROM feedback f
    JOIN respostas_admin ra ON ra.feedback_id = f.id
    WHERE f.user_id = ? AND f.status = 'lida'
    ORDER BY f.data_envio DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_lidos = $stmt->get_result();
$feedbacks_lidos = $result_lidos->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Os Meus Feedbacks</title>
    <link rel="stylesheet" href="../css/conta_compra.css">
</head>
<body>
<div class="bg">
    <div class="overlay"></div>
    <div class="content">
        <h1 style="color: white;">Os Meus Feedbacks</h1>

        <div class="estado-bloco">
            <h2>Por Ler</h2>
            <?php if ($feedbacks_porler): ?>
                <ul>
                <?php foreach ($feedbacks_porler as $fb): ?>
                    <li style="color: black; margin-bottom: 15px;">
                        <strong>Data:</strong> <?= $fb['data_envio'] ?><br>
                        <strong>Mensagem:</strong> <?= nl2br(htmlspecialchars($fb['feedback'])) ?>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p style="color: black;">Sem feedbacks por ler.</p>
            <?php endif; ?>
        </div>

        <div class="estado-bloco">
            <h2>Lidos</h2>
            <?php if ($feedbacks_lidos): ?>
                <ul>
                <?php foreach ($feedbacks_lidos as $fb): ?>
                    <li style="color: black; margin-bottom: 15px;">
                        <strong>Data:</strong> <?= $fb['data_envio'] ?><br>
                        <strong>Mensagem:</strong> <?= nl2br(htmlspecialchars($fb['feedback'])) ?><br><br>
                        <a href="ver_resposta.php?id=<?= $fb['id'] ?>" style="background: #fff; padding: 5px 10px; text-decoration: none; border-radius: 5px;">Visualizar Resposta</a>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p style="color: white;">Sem feedbacks lidos.</p>
            <?php endif; ?>
            <center>
        <a href="conta.php" class="btn voltar" style="margin-left: 10px;">Voltar</a>
            </center>
        </div>
    </div>
</div>
</body>
</html>
