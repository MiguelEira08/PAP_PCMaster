<?php
session_start();
include_once __DIR__ . '/../db.php';
include_once __DIR__ . '/../cabecindex.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit();
}

$id = $_SESSION['user_id'];

$encomendas_pendente = [];
$encomendas_caminho = [];
$encomendas_entregue = [];

$stmt = $conn->prepare("SELECT * FROM fim_compra WHERE utilizador_id = ? AND estado = ?");
$estados = ['Pendente', 'A caminho', 'Entregue'];

foreach ($estados as $estado) {
    $stmt->bind_param("is", $id, $estado);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($estado === 'Pendente') {
        $encomendas_pendente = $result->fetch_all(MYSQLI_ASSOC);
    } elseif ($estado === 'A caminho') {
        $encomendas_caminho = $result->fetch_all(MYSQLI_ASSOC);
    } elseif ($estado === 'Entregue') {
        $encomendas_entregue = $result->fetch_all(MYSQLI_ASSOC);
    }
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>As Minhas Encomendas</title>
  <link rel="stylesheet" href="../css/conta_compra.css">
  <link rel="icon" type="image/png" href="../imagens/icon.png">
</head>
<body>
<div class="bg">
  <div class="overlay"></div>
  <div class="content">

<h1 style="color: white; width: 100%; text-align: center; margin-bottom: 30px;">Estado das Minhas Encomendas</h1>

<div class="estado-bloco">
    <h2 class="titulo-estado">Pendentes</h2>
    <div class="cards-container">
        <?php if ($encomendas_pendente): ?>
            <?php foreach ($encomendas_pendente as $enc): ?>
                <div class="encomenda-card" onclick="window.location.href='ver_encomenda.php?id=<?= $enc['id'] ?>'">
                    <div class="card-header">
                        <span class="order-id">#<?= $enc['id'] ?></span>
                        <span class="status-badge pendente">Pendente</span>
                    </div>
                    <div class="card-body">
                        <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($enc['data_compra'])) ?></p>
                        <p><strong>Morada:</strong> <?= htmlspecialchars($enc['rua']) ?></p>
                        <p><?= htmlspecialchars($enc['distrito']) ?> (<?= $enc['codigo_postal'] ?>)</p>
                    </div>
                    
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="empty-msg">Sem encomendas pendentes.</p>
        <?php endif; ?>
    </div>
</div>

<div class="estado-bloco">
    <h2 class="titulo-estado">A Caminho</h2>
    <div class="cards-container">
        <?php if ($encomendas_caminho): ?>
            <?php foreach ($encomendas_caminho as $enc): ?>
                <div class="encomenda-card" onclick="window.location.href='ver_encomenda.php?id=<?= $enc['id'] ?>'">
                    <div class="card-header">
                        <span class="order-id">#<?= $enc['id'] ?></span>
                        <span class="status-badge caminho">A caminho</span>
                    </div>
                    <div class="card-body">
                        <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($enc['data_compra'])) ?></p>
                        <p><strong>Morada:</strong> <?= htmlspecialchars($enc['rua']) ?></p>
                        <p><?= htmlspecialchars($enc['distrito']) ?> (<?= $enc['codigo_postal'] ?>)</p>
                    </div>
                   
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="empty-msg">Nenhuma encomenda em trânsito.</p>
        <?php endif; ?>
    </div>
</div>

<div class="estado-bloco">
    <h2 class="titulo-estado">Entregues</h2>
    <div class="cards-container">
        <?php if ($encomendas_entregue): ?>
            <?php foreach ($encomendas_entregue as $enc): ?>
                <div class="encomenda-card entregue-hover" onclick="window.location.href='ver_encomenda.php?id=<?= $enc['id'] ?>'">
                    <div class="card-header">
                        <span class="order-id">#<?= $enc['id'] ?></span>
                        <span class="status-badge entregue">Entregue</span>
                    </div>
                    <div class="card-body">
                        <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($enc['data_compra'])) ?></p>
                        <p><strong>Morada:</strong> <?= htmlspecialchars($enc['rua']) ?></p>
                        <p><?= htmlspecialchars($enc['distrito']) ?> (<?= $enc['codigo_postal'] ?>)</p>
                    </div>
                    
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="empty-msg">Ainda não recebeu encomendas.</p>
        <?php endif; ?>
    </div>
    
</div>
       <div style="width: 100%; text-align: center; margin-top: 30px; margin-bottom: 50px;">
    <a href="javascript:history.back()" class="botao-voltar" style="text-decoration: none;">
        ← Voltar
    </a>
</div>

</div>

</div>
</div>
</body>
</html>
