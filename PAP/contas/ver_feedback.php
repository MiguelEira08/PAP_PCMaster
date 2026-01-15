<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../db.php';
include_once __DIR__ . '/../cabecindex.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Feedbacks Por Ler
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

// Feedbacks Lidos
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
    <link rel="icon" type="image/png" href="../imagens/icon.png">
    <title>Os Meus Feedbacks</title>
    <link rel="stylesheet" href="../css/conta_compra.css">
</head>
<body>
    <a href="./conta.php" class="voltar-fixo">← Voltar</a>

    <div class="bg">
        <div class="overlay"></div>
        <h1 style="margin-top: 50px; z-index: 2;">Os Meus Feedbacks</h1>
        
        <div class="content">
            <div class="estado-bloco">
                <h2 class="titulo-estado">Por Ler</h2>
                <div class="cards-container">
                    <?php if ($feedbacks_porler): ?>
                        <?php foreach ($feedbacks_porler as $fb): ?>
                            <div class="encomenda-card">
                                <div class="card-header">
                                    <strong><span class="order-id">ID #<?= $fb['id'] ?></span></strong>
                                    <span class="status-badge pendente">Pendente</span>
                                </div>
                                <div class="card-body">
                                    <p style="color: #333;"><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($fb['data_envio'])) ?></p>
                                    <p style="color: #333;"><strong>Mensagem:</strong> <?= nl2br(htmlspecialchars($fb['feedback'])) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="empty-msg">Sem feedbacks por ler.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="estado-bloco">
                <h2 class="titulo-estado">Lidos</h2>
                <div class="cards-container">
                    <?php if ($feedbacks_lidos): ?>
                        <?php foreach ($feedbacks_lidos as $fb): ?>
                            <div class="encomenda-card" onclick="window.location.href='ver_resposta.php?id=<?= $fb['id'] ?>'">
                                <div class="card-header">
                                    <strong><span class="order-id">ID #<?= $fb['id'] ?></span></strong>
                                    <span class="status-badge entregue">Lido</span>
                                </div>
                                <div class="card-body">
                                    <p style="color: #333;"><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($fb['data_envio'])) ?></p>
                                    <p style="color: #333;"><strong>Mensagem:</strong> <?= nl2br(htmlspecialchars($fb['feedback'])) ?></p>
                                    <div style="margin-top: 10px; text-align: right;">
                                        <small style="color: #df7700; font-weight: bold;">Clique para ver resposta →</small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="empty-msg">Sem feedbacks lidos.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>