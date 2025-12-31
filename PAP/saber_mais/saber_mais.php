<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../db.php';
include_once __DIR__ . '/../cabecindex.php';

if (!isset($utilizador)) {
    $utilizador = ['id' => 0]; 
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/loja.css">
    <div class="bg">
    <div class="overlay">
    <div class="content">
  <div class="caixa-container">
    <a href="sobre_nos.php" class="caixa">
      <img src="../imagens/sobre_nos.png" alt="imagem" class="caixa-imagem">Sobre Nós</a>
    
    <a href="suporte.php" class="caixa">
      <img src="../imagens/suporte.png" alt="imagem" class="caixa-imagem">Suporte</a>
  </div>
</div>
  <div class="caixa-container">
  <div class="botao-link"  onclick="window.location.href='../index/index.php';">
        Voltar atrás
</div></div>
</div>
</body>
</html>