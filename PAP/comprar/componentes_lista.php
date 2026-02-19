<?php
session_start();
$_SESSION['voltar_inteligente'] = 'loja.php';

include_once __DIR__ . '/../botao_voltar.php';
include_once '../db.php';

$tipoSelecionado = isset($_GET['tipo']) ? strtolower(trim($_GET['tipo'])) : '';
$busca           = isset($_GET['q'])    ? trim($_GET['q'])             : '';

$sql    = "SELECT * FROM componentes WHERE 1=1";
$params = [];
$types  = '';

if ($tipoSelecionado !== '') {
    $sql .= " AND LOWER(tipo) = ?";
    $params[] = $tipoSelecionado;
    $types .= 's';
}

if ($busca !== '') {
    $sql .= " AND LOWER(nome) LIKE LOWER(CONCAT('%', ?, '%'))";
    $params[] = $busca;
    $types .= 's';
}

$sql .= " ORDER BY nome ASC";

if ($params) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = mysqli_query($conn, $sql);
}

if ($result && mysqli_num_rows($result) > 0) {
    echo '<div class="grid-produtos" style="display:flex; flex-wrap:wrap; gap:24px; justify-content:flex-start;">';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<div class="cartao-produto" style="width:300px; height:400px; display:flex; flex-direction:column; justify-content:space-between; box-sizing:border-box;">';
            echo '<div class="cartao-imagem" style="width:100%; height:250px; display:flex; align-items:center; justify-content:center; overflow:hidden;">';
                echo '<img src="../imagens/' . $row['caminho_arquivo'] . '" alt="Imagem de ' . htmlspecialchars($row['nome']) . '" style="max-width:100%; max-height:100%; object-fit:contain;">';
            echo '</div>';
            echo '<div class="cartao-detalhes" style="flex:1; display:flex; flex-direction:column; justify-content:space-between; align-items:center;">';
                echo '<div class="manrope-titulo" text-align:center;">' . htmlspecialchars($row['nome']) . '</div>';
                echo '<h4 class="preco" style="margin:5px 0 10px;">€' . number_format($row['preco'], 2) . '</h4>';
                echo '<a href="produto_componente.php?id=' . $row['id'] . '" class="btn-adicionar" style="margin-bottom:10px;">Visualizar</a>';
            echo '</div>';
        echo '</div>';
    }
    echo '</div>';
} else {
    echo '<h3 class="nao-encontrado">Nenhum componente encontrado.</h3>';
}
?>
