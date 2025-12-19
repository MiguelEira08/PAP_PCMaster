<?php
session_start();
include_once '../db.php';

$tipoSelecionado = isset($_GET['tipo']) ? strtolower(trim($_GET['tipo'])) : '';
$busca           = isset($_GET['q'])    ? trim($_GET['q'])             : '';

$sql = "SELECT * FROM componentes WHERE 1=1";
$params = [];
$types = '';

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
    echo '<div class="produtos-linha">';
    while ($row = mysqli_fetch_assoc($result)) {
        $produtoJS = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
        echo '<div class="produto-card">';
        echo '<img class="produto-imagem" src="../imagens/' . $row["caminho_arquivo"] . '" alt="' . htmlspecialchars($row["nome"]) . '">';
        echo '<h2 class="produto-nome">' . htmlspecialchars($row["nome"]) . '</h2>';
        echo '<a class="botao-visualizar" href="javascript:void(0)" onclick="adicionarComparacao(' . $produtoJS . ')">Adicionar à comparação</a>';
        echo '</div>';
    }
    echo '</div>';
} else {
    echo '<p>Nenhum componente encontrado.</p>';
}
?>
