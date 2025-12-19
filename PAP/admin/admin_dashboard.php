<?php
session_start();
include_once __DIR__ . '/../db.php';
include_once __DIR__ . '/../cabecindex.php';
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/admin_dash.css">
    <title>Loja</title>
    <link rel="icon" type="image/png" href="../imagens/icon.png">
</head>
<body>
    <div class= "bg">
    <div class="overlay"></div>
    <br><br>
    <div class="content">
  <div class="caixa-container">
    <a href="admin_componentes.php" class="caixa">
      <img src="../imagens/comp.png" alt="Comprar" class="caixa-imagem">Gerir Componentes</a>
    
    <a href="admin_perifericos.php" class="caixa">
      <img src="../imagens/per.png" alt="Comprar" class="caixa-imagem">Gerir Periféricos</a>
  </div>
  <div class="caixa-container">
    <a href="admin_utilizadores.php" class="caixa">
      <img src="../imagens/user.png" alt="Comprar" class="caixa-imagem">Gerir Utilizadores</a>
  </div>
  <div class="caixa-container">
    <a href="feedback_cliente.php" class="caixa">
      <img src="../imagens/feedback.png" alt="Comprar" class="caixa-imagem">Feedbacks   </a>
    
    <a href="admin_compras.php" class="caixa">
      <img src="../imagens/compra.png" alt="Comprar" class="caixa-imagem">Gerir Compras Feitas</a>
</div>
</div>
</div>
</body>
</html>