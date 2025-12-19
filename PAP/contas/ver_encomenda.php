<?php
session_start();
include_once __DIR__ . '/../db.php';
include_once __DIR__ . '/../cabecindex.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit();
}

$erro = '';
$encomenda = null;
$itens = [];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $erro = 'Encomenda inválida.';
} else {
    $id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT * FROM fim_compra WHERE id = ? AND utilizador_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        $erro = 'Encomenda não encontrada.';
    } else {
        $encomenda = $result->fetch_assoc();

        $stmt = $conn->prepare("SELECT * FROM fim_compra_itens WHERE compra_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $itens = $result->fetch_all(MYSQLI_ASSOC);
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>Ver Encomenda</title>
  <link rel="stylesheet" href="../css/conta_compra.css">
</head>
<body>
<div class="bg">
  <div class="overlay"></div>
  <div class="content">
    <?php if ($erro): ?>
      <form>
        <p class="error-message"><?= htmlspecialchars($erro) ?></p>
        <a href="estado_encomendas.php" class="voltar-btn">Voltar</a>
      </form>
    <?php else: ?>
      <form>
        <h2>Detalhes da Encomenda</h2>
        <p><strong>Morada:</strong> <?= htmlspecialchars($encomenda['rua']) ?>, <?= htmlspecialchars($encomenda['distrito']) ?> (<?= htmlspecialchars($encomenda['codigo_postal']) ?>)</p>
        <p><strong>Data:</strong> <?= htmlspecialchars($encomenda['data_compra']) ?></p>
        <p><strong>Estado:</strong> <?= htmlspecialchars($encomenda['estado']) ?></p>

        <h3 style="margin-top: 20px;">Produtos:</h3>
        <?php if ($itens): ?>
          <ul style="text-align: left; padding-left: 0;">
            <?php foreach ($itens as $item): ?>
              <li style="margin-bottom: 8px; list-style: none;">
                <?= htmlspecialchars($item['tipo_produto']) ?>: <br>
                <?= htmlspecialchars($item['nome_produto']) ?> <br>
                Quantidade: <?= $item['quantidade'] ?> <br>
                Preço: €<?= number_format($item['preco'], 2, ',', '.') ?>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p>Sem produtos nesta encomenda.</p>
        <?php endif; ?>

        <a href="estado_encomenda.php" class="voltar-btn">Voltar</a>
      </form>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
