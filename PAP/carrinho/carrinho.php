<?php
session_start();
include_once __DIR__ . '/../db.php';
include_once __DIR__ . '/../cabecindex.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit;
}

$id_utilizador = $_SESSION['user_id'];

if ($conn->connect_error) {
    die("Erro na ligação: " . $conn->connect_error);
}

if (isset($_GET['compra_ok'])) {
    echo '<script>alert("Compra finalizada com sucesso!");</script>';
}

$componentes = $conn->query("
    SELECT c.id AS carrinho_id, c.quantidade, comp.nome, comp.preco, comp.descricao, comp.caminho_arquivo, comp.marca
    FROM carrinho c
    JOIN componentes comp ON c.id_produto = comp.id
    WHERE c.tipo_produto = 'componente' AND c.id_utilizador = $id_utilizador
");

$perifericos = $conn->query("
    SELECT c.id AS carrinho_id, c.quantidade, peri.nome, peri.preco, peri.descricao, peri.caminho_arquivo, peri.marca
    FROM carrinho c
    JOIN perifericos peri ON c.id_produto = peri.id
    WHERE c.tipo_produto = 'periferico' AND c.id_utilizador = $id_utilizador
");

$total_geral = 0;
?>

<!DOCTYPE html>
<html lang="pt">
<head> 
  <meta charset="UTF-8">
  <title>Loja Componentes</title>
  <link rel="stylesheet" href="../css/carrinho.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Amiko:wght@600&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="../imagens/icon.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
<div class="bg">
  <div class="overlay"></div>
  <div class="content">
    <h2 align="center" class="amiko-semibold">Produtos no Carrinho</h2>
    <br>
    <h1 align="center" class="amiko-semibold"><b>Olá, <?= htmlspecialchars($_SESSION['nome']) ?>!</b></h1>
    <br>
    <h4 align="center" class="amiko-semibold">Aqui estão os produtos que adicionaste ao teu carrinho:</h4>
    <br>
    <h4 align="center" class="amiko-semibold">Podes remover produtos do carrinho ou finalizar a compra.</h4>
    <br><br>

    <h1 class="amiko-semibold">Componentes</h1>
    <br>
    <?php if ($componentes && $componentes->num_rows > 0): ?>
      <div class="grade-produtos">
        <?php while ($row = $componentes->fetch_assoc()):
          $subtotal = $row['preco'] * $row['quantidade'];
          $total_geral += $subtotal;
        ?>
          <div class="produto-card">
            <img src="../imagens/<?= htmlspecialchars($row['caminho_arquivo']) ?>" width="200" alt="<?= htmlspecialchars($row['nome']) ?>">
            <p class="kalam-bold"><strong><?= htmlspecialchars($row['nome']) ?></strong></p>
            <p class="kalam-light"><?= htmlspecialchars($row['descricao']) ?></p>
            <p class="kalam-light"><strong>Marca: <?= htmlspecialchars($row['marca']) ?></strong></p>
            <p class="kalam-light"><strong>Preço: <?= number_format($row['preco'], 2) ?> € </strong></p>
            <p class="kalam-light"><strong>Quantidade: <?= (int)$row['quantidade'] ?></strong></p>
            <p><strong> Subtotal: <?= number_format($subtotal, 2) ?> €</strong></p>
            <form method="post" action="remover_do_carrinho.php">
              <input type="hidden" name="carrinho_id" value="<?= (int)$row['carrinho_id'] ?>">
             <div align="center"> <button type="submit" class="botao-visualizar">Remover</button></div>
            </form>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <h4 align="center" class="amiko-semibold">Não tens componentes no carrinho.</h4>
    <?php endif; ?>

    <br>
    <h1 class="amiko-semibold">Periféricos</h1>
    <br>
    <?php if ($perifericos && $perifericos->num_rows > 0): ?>
      <div class="grade-produtos">
        <?php while ($row = $perifericos->fetch_assoc()):
          $subtotal = $row['preco'] * $row['quantidade'];
          $total_geral += $subtotal;
        ?>
          <div class="produto-card">
            <img src="../imagens/<?= htmlspecialchars($row['caminho_arquivo']) ?>" width="200" alt="<?= htmlspecialchars($row['nome']) ?>">
            <p><strong><?= htmlspecialchars($row['nome']) ?></strong></p>
            <br>
            <p class="kalam-light"><?= htmlspecialchars($row['descricao']) ?></p>
            <p class="kalam-bold">Marca: <?= htmlspecialchars($row['marca']) ?></p>
            <p class="kalam-bold"><strong>Preço: <?= number_format($row['preco'], 2) ?> €</strong></p>
            <p class="kalam-bold"><strong>Quantidade: <?= (int)$row['quantidade'] ?></strong></p>
            <p><strong>Subtotal: <?= number_format($subtotal, 2) ?> €</strong></p>
            <form method="post" action="remover_do_carrinho.php">
              <input type="hidden" name="carrinho_id" value="<?= (int)$row['carrinho_id'] ?>">
             <div align="center"> <button type="submit" class="botao-visualizar">Remover</button></div>
            </form>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <h4 align="center">Não tens periféricos no carrinho.</h4>
    <?php endif; ?>
    <br><br>

    <h3 align="center" class="total-geral"><b>Total Geral: <?= number_format($total_geral, 2) ?> €</b></h3>

    <?php if (($componentes && $componentes->num_rows > 0) || ($perifericos && $perifericos->num_rows > 0)): ?>
      <form method="get" action="finalizar_compra.php" style="text-align: center;">
        <br>
      <div align="center"><button type="submit" class="botao-visualizar">Finalizar Compra</button></div>
      </form>

    <?php endif; ?>
    
</div>
</body>
</html>
