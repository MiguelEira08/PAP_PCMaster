<?php
session_start();
include_once __DIR__ . '/../db.php';
include_once __DIR__ . '/../cabecindex.php';

?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/loja.css">
    <title>Loja</title>
    <link rel="icon" type="image/png" href="../imagens/icon.png">
</head>
<body>  
    <div class= "bg">
    <div class="overlay">
    <div class="content">
  <div class="caixa-container">
    <a href="componentes.php" class="caixa">
      <img src="../imagens/componentes.avif" alt="imagem" class="caixa-imagem">Comprar Componentes</a>
    
    <a href="perifericos.php" class="caixa">
      <img src="../imagens/perifericos.png" alt="imagem" class="caixa-imagem">Comprar Periféricos</a>
  </div>
</div>
  <div class="caixa-container">
  <div class="botao-link"  onclick="window.location.href='../index/index.php';">Voltar ao início</div>

</div>
</div>


</body>
</html>