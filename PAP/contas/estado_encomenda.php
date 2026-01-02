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

    <h1 style="color: white;">Estado das Minhas Encomendas</h1>

    <div class="estado-bloco" align="center">
      <h2>Pendentes</h2>
      <?php if ($encomendas_pendente): ?>
        <ul>
          <?php foreach ($encomendas_pendente as $enc): ?>
            <li>
            <a href="ver_encomenda.php?id=<?= $enc['id'] ?>" style="text-decoration: none; color: inherit;">
                <strong>ID:</strong> #<?= $enc['id'] ?><br>
                <p><strong>Morada:</strong> <?= htmlspecialchars($enc['rua']) ?>, <?= htmlspecialchars($enc['distrito']) ?> (<?= $enc['codigo_postal'] ?>)</p>
                <p><strong>Data:</strong> <?= $enc['data_compra'] ?></p>
                <p><strong>Estado:</strong> <?= htmlspecialchars($enc['estado']) ?></p>
            </a>
            </li>

          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>Sem encomendas pendentes.</p>
      <?php endif; ?>
    </div>

    <div class="estado-bloco">
      <h2>A Caminho</h2>
      <?php if ($encomendas_caminho): ?>
        <ul>
          <?php foreach ($encomendas_caminho as $enc): ?>
            <li>
            <a href="ver_encomenda.php?id=<?= $enc['id'] ?>" style="text-decoration: none; color: inherit;">
                <strong>ID:</strong> #<?= $enc['id'] ?><br>
                <p><strong>Morada:</strong> <?= htmlspecialchars($enc['rua']) ?>, <?= htmlspecialchars($enc['distrito']) ?> (<?= $enc['codigo_postal'] ?>)</p>
                <p><strong>Data:</strong> <?= $enc['data_compra'] ?></p>
                <p><strong>Estado:</strong> <?= htmlspecialchars($enc['estado']) ?></p>
            </a>
            </li>

          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>Sem encomendas a caminho.</p>
      <?php endif; ?>
    </div>

    <div class="estado-bloco">
      <h2>Entregues</h2>
      <?php if ($encomendas_entregue): ?>
        <ul>
          <?php foreach ($encomendas_entregue as $enc): ?>
            <li>
            <a href="ver_encomenda.php?id=<?= $enc['id'] ?>" style="text-decoration: none; color: inherit;">
                <strong>ID:</strong> #<?= $enc['id'] ?><br>
                <p><strong>Morada:</strong> <?= htmlspecialchars($enc['rua']) ?>, <?= htmlspecialchars($enc['distrito']) ?> (<?= $enc['codigo_postal'] ?>)</p>
                <p><strong>Data:</strong> <?= $enc['data_compra'] ?></p>
                <p><strong>Estado:</strong> <?= htmlspecialchars($enc['estado']) ?></p>
            </a>
            </li>

          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>Sem encomendas entregues.</p>
        <br><br>
            <div class="caixa-container">
            <div class="botao-link"  onclick="window.location.href='./conta.php';">Voltar atrás</div>
            </div>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>
