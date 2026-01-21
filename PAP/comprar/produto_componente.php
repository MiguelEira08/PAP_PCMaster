<?php
session_start();
include_once __DIR__ . '/../db.php';
include_once __DIR__ . '/../cabecindex.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "SELECT * FROM componentes WHERE id = $id";
$result = mysqli_query($conn, $sql);
$produto = mysqli_fetch_assoc($result);

if (!$produto) {
    echo "<p>Produto não encontrado.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
  <link rel="icon" type="image/png" href="../imagens/icon.png">
    <title><?= htmlspecialchars($produto['nome']) ?> - Detalhes</title>
    <link rel="stylesheet" href="../css/explorar.css">
    <link rel="icon" type="image/png" href="../imagens/icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@200&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="bg">
        <div class="overlay"></div>
        <div class="content">
            <nav class="breadcrumb">
            </nav>
            <div class="pagina-produto">
                <div class="produto-detalhe">
                    <div class="imagem-grande">
                        <img src="../imagens/<?= htmlspecialchars($produto['caminho_arquivo']) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
                    </div>
                    <div class="info-produto">
                        <h1 ><?= htmlspecialchars($produto['nome']) ?></h1>
                        <p class="descricao"><?= !empty($produto['descricao']) ? nl2br(htmlspecialchars($produto['descricao'])) : 'Sem descrição disponível.' ?></p>
                        <p class="preco">Preço: <strong>€<?= number_format($produto['preco'], 2) ?></strong></p>
                        <p class="stock">Stock disponível: <?= (int)$produto['stock'] ?></p>

                       <form action="../carrinho/adicionar_ao_carrinho.php" method="post" class="form-carrinho">
    <input type="hidden" name="id_produto" value="<?= (int)$produto['id'] ?>">
    
  
    <input type="hidden" name="tipo_produto" value="componente"> 

    <label for="quantidade_<?= (int)$produto['id'] ?>">Quantidade:</label>
    <input type="number"
           id="quantidade_<?= (int)$produto['id'] ?>"
           name="quantidade"
           value="1"
           min="1"
           max="<?= (int)$produto['stock'] ?>"
           required>

   
    <button type="submit" name="acao" value="carrinho" class="btn-adicionar">Adicionar ao carrinho</button>
    <button type="submit" name="acao" value="comprar" class="btn-adicionar">Comprar já</button>
    <center>
<button type="button" class="btn-adicionar2" onclick="window.location.href='./componentes.php';">Voltar</button>
    </center>

</form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if (isset($_GET['sucesso'])): ?>
<div id="toast" class="toast">Produto adicionado ao carrinho!</div>
<script>
  const toast = document.getElementById("toast");
  toast.classList.add("show");
  setTimeout(() => {
    toast.classList.remove("show");
  }, 3000);
</script>
<style>
.toast {
  position: fixed;
  top: 20px;
  right: 20px;
  background-color: burlywood;
  font-family: 'Poppins', sans-serif;
  color: black;
  padding: 15px 25px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
  z-index: 9999;
  opacity: 0;
  transform: translateY(-20px);
  transition: opacity 0.3s ease, transform 0.3s ease;
}
.toast.show {
  opacity: 1;
  transform: translateY(0);
}
</style>
<?php endif; ?>
</body>
</html>
