<?php
include_once '../pagcabecs/cabec.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($utilizador)) {
    $utilizador = ['id' => 0]; 
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/conta.css">
    <div class="bg">
    <div class="overlay"></div>
    <div class="content">
  <div class="caixa-container">
    <a href="gerir_conta.php" class="caixa">
      <img src="../imagens/user.png" alt="imagem" class="caixa-imagem">Gerir Conta</a>

    <a href="estado_encomenda.php" class="caixa">
      <img src="../imagens/compra.png" alt="imagem" class="caixa-imagem">Estado da Encomenda</a>
  </div>
  <div class="caixa-container">
    <a href="enviar_feedback.php" class="caixa">
      <img src="../imagens/feedback.png" alt="imagem" class="caixa-imagem">Enviar Feedback</a>

    <a href="ver_feedback.php" class="caixa">
      <img src="../imagens/feedback.png" alt="imagem" class="caixa-imagem">Ver Feedback</a>
  </div>


</div>
</div>


</body>
</html>