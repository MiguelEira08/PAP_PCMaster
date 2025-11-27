<?php
include_once 'cabecindex.php';
include_once 'db.php'; 
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>Página Inicial</title>
  <link rel="stylesheet" href="../css/naveindex.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Amiko:wght@600&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="./imagens/icon.png">
</head>
<body>
<?php if (isset($_GET['logout']) && $_GET['logout'] == 1): ?>
  <div id="logout-alert" style="
    position: fixed;
    top: 20px;
    right: 20px;
    background-color: #d4edda;
    color: #155724;
    padding: 12px 20px;
    border: 1px solid #c3e6cb;
    border-radius: 5px;
    font-family: Calibri, sans-serif;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    z-index: 9999;
  ">
    Logout realizado com sucesso.
  </div>

  <script>
    setTimeout(() => {
      const alert = document.getElementById('logout-alert');
      if (alert) alert.style.display = 'none';
    }, 3000); 
  </script>
<?php endif; ?>
  <div class="bg">
    <div class="overlay"></div>
    <div class="content">
      <h1 align="center" class="amiko-semibold"> <b>Encontre aqui os melhores produtos de Informática</b></h1>
      <br>
      <p align="center" class="amiko-semibold">Aceda à nossa loja e aproveite os melhores produtos de informática para si
      

      </p>
     
  <div class="caixa-container">
  <div class="botao-link"  onclick="window.location.href='./comprar/loja.php';">
        Compre aqui
  </div>
  <div class="botao-link" onclick="window.location.href='./comparar/comparar.php';">
        Compare aqui
  </div>
</div>
  </div>

</body>
</html>
