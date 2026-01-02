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
  'HyperX','Razer','Logitech','Corsair','Steelseries','Asus Rog','NPlay','Gigabyte',
  'Mars Gaming','HP','AOC','Asus','LG','BenQ','MSI','Acer','Omen','Samsung','Apple'
];

$tipos = [
  'Rato','Teclado','Fones','Tapete','Monitor'
];

$stock = filter_input(INPUT_GET, 'stock', FILTER_SANITIZE_NUMBER_INT);
$tipo  = filter_input(INPUT_GET, 'tipo', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$marca = filter_input(INPUT_GET, 'marca', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$queryFiltros = http_build_query([
    'stock' => $stock ?? '',
    'tipo'  => $tipo ?? '',
    'marca' => $marca ?? '',
]);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $erro = 'ID inválido.';
} else {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM perifericos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $erro = 'Periférico não encontrado.';
    } else {
        $periferico = $result->fetch_assoc();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = trim($_POST['nome']);
            $preco = floatval($_POST['preco']);
            $descricao = trim($_POST['descricao']);
            $stockPost = intval($_POST['stock']);
            $marcaPost = $_POST['marca'];
            $tipoPost = $_POST['tipo'];
            $imagemAtual = $periferico['caminho_arquivo'];
            $novaImagem = $imagemAtual;

            if (!empty($_FILES['imagem']['name'])) {
                $tmp = $_FILES['imagem']['tmp_name'];
                $nomeOriginal = basename($_FILES['imagem']['name']);
                $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
                $permitidas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

                if (in_array($extensao, $permitidas)) {
                    $nomeFinal = uniqid() . "." . $extensao;
                    $destino = '../imagens/' . $nomeFinal;
                    if (move_uploaded_file($tmp, $destino)) {
                        if (file_exists('../imagens/' . $imagemAtual)) {
                            unlink('../imagens/' . $imagemAtual);
                        }
                        $novaImagem = $nomeFinal;
                    } else {
                        $erro = 'Falha ao guardar nova imagem.';
                    }
                } else {
                    $erro = 'Formato de imagem inválido.';
                }
            }

            if (empty($erro)) {
                $stmt = $conn->prepare("UPDATE perifericos SET nome=?, preco=?, descricao=?, caminho_arquivo=?, stock=?, marca=?, tipo=? WHERE id=?");
                $stmt->bind_param("sdssissi", $nome, $preco, $descricao, $novaImagem, $stockPost, $marcaPost, $tipoPost, $id);
                if ($stmt->execute()) {
                    header("Location: ../admin/admin_perifericos.php?" . $queryFiltros . "#linha-" . $id);
                    exit;
                } else {
                    $erro = 'Erro ao atualizar periférico.';
                }
                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>Editar Periférico</title>
  <link rel="stylesheet" href="../css/admin_criar.css">
</head>
<body>
<div class="bg">
  <div class="overlay"></div>
  <div class="content">
    <form method="POST" enctype="multipart/form-data">
        <h2>Editar Periférico</h2>

        <?php if ($erro): ?>
            <p class="error-message"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>

        <?php if (isset($periferico)): ?>
            <label>Nome:</label>
            <input type="text" name="nome" value="<?= htmlspecialchars($periferico['nome']) ?>" required>

            <label>Preço (€):</label>
            <input type="number" step="0.01" name="preco" value="<?= htmlspecialchars($periferico['preco']) ?>" required>

            <label>Descrição:</label>
            <textarea 
                name="descricao" 
                rows="6"
                style="resize: none; width: 100%; height: 120px; font-family: inherit; font-size: 1rem; padding: 8px; border-radius: 4px; border: 1px solid #ccc;"
                required
            ><?= htmlspecialchars($periferico['descricao']) ?></textarea>

            <label>Stock:</label>
            <input type="number" name="stock" value="<?= htmlspecialchars($periferico['stock']) ?>" required>

            <label>Marca:</label>
            <select name="marca" required>
              <?php foreach ($marcas as $m): ?>
                <option value="<?= $m ?>" <?= ($periferico['marca'] === $m ? 'selected' : '') ?>><?= $m ?></option>
              <?php endforeach; ?>
            </select>

            <label>Tipo:</label>
            <select name="tipo" required>
              <?php foreach ($tipos as $t): ?>
                <option value="<?= $t ?>" <?= ($periferico['tipo'] === $t ? 'selected' : '') ?>><?= $t ?></option>
              <?php endforeach; ?>
            </select>

            <center>
              <label>Imagem atual:</label><br>
              <img src="../imagens/<?= htmlspecialchars($periferico['caminho_arquivo']) ?>" alt="Atual" style="width: 100%; max-width: 200px; margin-bottom: 12px; border-radius: 8px;">
            </center>

            <label>Alterar imagem:</label>
            <input type="file" name="imagem" accept="image/*">
            <br><br>
           <div align="center"><button type="submit" class="botao">Guardar alterações</button></div> 
            <br>
        <div align="center"><button type="button" class="botao2" onclick="window.location.href='../admin/admin_perifericos.php';">Voltar</button></div>
                <?php endif; ?>
    </form>
  </div>
</div>
</body>
</html>
