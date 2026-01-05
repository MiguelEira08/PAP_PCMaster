<?php
session_start();
include_once __DIR__ . '/../db.php';
include_once __DIR__ . '/../cabecindex.php';
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../index/index.php");
    exit;
}

$erro = '';
$sucesso = '';

$marcas = [
    'HyperX','Razer','Logitech','Corsair','Gigabyte','Steelseries','Asus Rog','NPlay',
    'Mars Gaming','HP','AOC','Asus','LG','BenQ','MSI','Acer','Omen','Samsung','Apple'
];

$tipos = [
    'Rato','Teclado','Fones','Tapete','Monitor'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $preco = floatval($_POST['preco']);
    $descricao = trim($_POST['descricao']);
    $stock = intval($_POST['stock']);
    $marca = $_POST['marca'];
    $tipo = $_POST['tipo'];

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['imagem']['tmp_name'];
        $nomeOriginal = basename($_FILES['imagem']['name']);
        $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (in_array($extensao, $permitidas)) {
            $nomeFinal = uniqid() . "." . $extensao;
            $destino = '../imagens/' . $nomeFinal;
            move_uploaded_file($tmp, $destino);

            $stmt = $conn->prepare("INSERT INTO perifericos (nome, preco, descricao, caminho_arquivo, stock, marca, tipo) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sdssiss", $nome, $preco, $descricao, $nomeFinal, $stock, $marca, $tipo);

                if ($stmt->execute()) {
                    $sucesso = 'Periférico adicionado com sucesso!';
                } else {
                    $erro = 'Erro ao adicionar periférico.';
                }

                $stmt->close();
            } else {
                $erro = 'Erro na preparação da query: ' . $conn->error;
            }
        } else {
            $erro = 'Formato de imagem inválido.';
        }
    } else {
        $erro = 'Imagem obrigatória.';
    }
}
?>


<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Componente</title>
    <link rel="stylesheet" href="../css/admin_criar.css">
    
</head>
<body>
<div class="bg">
  <div class="overlay"></div>
    <div class="content">
    <form method="POST" enctype="multipart/form-data">
        <h2>Adicionar Periférico</h2>

        <?php if ($erro): ?>
            <p class="error-message"><?= htmlspecialchars($erro) ?></p>
        <?php elseif ($sucesso): ?>
            <p style="color: green; font-weight: bold;"><?= htmlspecialchars($sucesso) ?></p>
        <?php endif; ?>

        <label for="nome">Nome:</label>
        <input type="text" name="nome" required>

        <label for="preco">Preço (€):</label>
        <input type="number" step="0.01" name="preco" required>

        <label for="descricao">Descrição:</label>
        <textarea 
            name="descricao" 
            id="descricao"
            rows="6"
            placeholder="Faz uma descrição do componente"
            style="resize: none; width: 100%; height: 120px; font-family: inherit; font-size: 1rem; padding: 8px; border-radius: 4px; border: 1px solid #ccc;"
            required
        ></textarea>

        <label for="stock">Stock:</label>
        <input type="number" name="stock" required>

        <label for="marca">Marca:</label>
        <select name="marca" required>
            <?php foreach ($marcas as $marcaOption): ?>
                <option value="<?= htmlspecialchars($marcaOption) ?>"><?= htmlspecialchars($marcaOption) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="tipo">Tipo:</label>
        <select name="tipo" required>
            <?php foreach ($tipos as $tipoOption): ?>
                <option value="<?= htmlspecialchars($tipoOption) ?>"><?= htmlspecialchars(ucfirst($tipoOption)) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="imagem">Imagem:</label>
        <input type="file" name="imagem" accept="image/*" required>
            <br><br>
           <div align="center"><button type="submit" class="botao">Adicionar Periférico</button></div> 
            <br>
        <div align="center"><button type="button" class="botao2" onclick="window.location.href='../admin/admin_perifericos.php';">Voltar</button></div>
        </form>
</div>
</div>
</body>
</html>
