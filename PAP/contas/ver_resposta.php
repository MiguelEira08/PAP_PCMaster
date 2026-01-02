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

if (!isset($_GET['id'])) {
    exit('ID inválido.');
}

$feedback_id = (int) $_GET['id'];

$stmt = $conn->prepare("
    SELECT f.feedback, f.data_envio, ra.resposta_admin, ra.nome_admin, ra.data_envio AS data_resposta
    FROM feedback f
    JOIN respostas_admin ra ON ra.feedback_id = f.id
    WHERE f.id = ? AND f.user_id = ?
");
$stmt->bind_param("ii", $feedback_id, $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    exit('Feedback não encontrado ou sem resposta.');
}

$stmt->bind_result($feedback_texto, $data_envio, $resposta_admin, $nome_admin, $data_resposta);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Ver Resposta</title>
    <link rel="stylesheet" href="../css/conta_compra.css">
</head>
<body>
<div class="bg">
    <div class="overlay"></div>
    <div class="content">
        <h1 style="color: white;">Resposta ao Seu Feedback</h1>

        <div class="estado-bloco">
            <h2>O Seu Feedback</h2>
            <p style="color: black;">
                <strong>Data: </strong> <?= htmlspecialchars($data_envio) ?><br>
                <strong>Feedvack: </strong><?= nl2br(htmlspecialchars($feedback_texto)) ?>
            </p>
        </div>

        <div class="estado-bloco">
            <h2>Resposta do Admin</h2>
            <p style="color: black;">
                <strong>Admin: </strong> <?= htmlspecialchars($nome_admin) ?><br>
                <strong>Data: </strong> <?= htmlspecialchars($data_resposta) ?><br>
                <strong>Resposta: </strong> <?= nl2br(htmlspecialchars($resposta_admin)) ?>
            </p>
            <div class="caixa-container">
            <div class="botao-link"  onclick="window.location.href='./ver_feedback.php';">Voltar atrás</div>
            </div>
        </div>
</div>
</div>
</body>
</html>
