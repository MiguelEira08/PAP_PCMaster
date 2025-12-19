<?php
session_start();
include_once __DIR__ . '/../db.php';
include_once __DIR__ . '/../cabecindex.php';

$stock = filter_input(INPUT_GET, 'stock', FILTER_SANITIZE_NUMBER_INT);
$tipo  = filter_input(INPUT_GET, 'tipo',  FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$marca = filter_input(INPUT_GET, 'marca', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$queryFiltros = http_build_query([
    'stock' => $stock ?? '',
    'tipo'  => $tipo ?? '',
    'marca' => $marca ?? '',
]);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Painel de Administração - Componentes</title>
    <link rel="stylesheet" href="../css/admin_produto.css">
    <link rel="icon" type="image/png" href="../imagens/icon.png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="bg">
    <div class="overlay"></div>
    <br><br><br>
    <div class="content">
        <h2>Gestão de Componentes</h2>

        <form method="GET" class="form-filtros">
            <label for="stock">Stock:</label>
            <select name="stock" id="stock">
                <option value="">Todos</option>
                <?php
                $resStock = mysqli_query($conn, "SELECT DISTINCT stock FROM componentes ORDER BY stock ASC");
                while ($s = mysqli_fetch_assoc($resStock)) {
                    $val      = $s['stock'];
                    $selected = ($stock !== null && $stock !== '' && (string)$val === (string)$stock) ? 'selected' : '';
                    echo '<option value="' . htmlspecialchars($val) . '" ' . $selected . '>' . htmlspecialchars($val) . '</option>';
                }
                ?>
            </select>

            <label for="tipo">Tipo:</label>
            <select name="tipo" id="tipo">
                <option value="">Todos</option>
                <?php
                $resTipos = mysqli_query($conn, "SELECT DISTINCT tipo FROM componentes ORDER BY tipo ASC");
                while ($t = mysqli_fetch_assoc($resTipos)) {
                    $sel = ($tipo !== null && $tipo !== '' && $t['tipo'] === $tipo) ? 'selected' : '';
                    echo '<option value="' . htmlspecialchars($t['tipo']) . '" ' . $sel . '>' . htmlspecialchars(ucfirst($t['tipo'])) . '</option>';
                }
                ?>
            </select>

            <label for="marca">Marca:</label>
            <select name="marca" id="marca">
                <option value="">Todas</option>
                <?php
                $resMarcas = mysqli_query($conn, "SELECT DISTINCT marca FROM componentes ORDER BY marca ASC");
                while ($m = mysqli_fetch_assoc($resMarcas)) {
                    $sel = ($marca !== null && $marca !== '' && $m['marca'] === $marca) ? 'selected' : '';
                    echo '<option value="' . htmlspecialchars($m['marca']) . '" ' . $sel . '>' . htmlspecialchars($m['marca']) . '</option>';
                }
                ?>
            </select>

            <button type="submit">Procurar</button>
            <a href="../admin_gestao/adicionar_componentes.php" class="btn criar" style="margin-left:10px;">Adicionar Componentes</a>
            <a href="../admin/admin_dashboard.php" class="btn voltar" style="margin-left:10px;">Voltar</a>
        </form>

        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Marca</th>
                        <th>Preço (€)</th>
                        <th>Stock</th>
                        <th>Imagem</th>
                        <th>Editar / Apagar</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $sql    = "SELECT * FROM componentes WHERE 1=1";
                $params = [];
                $types  = '';

                if ($stock !== null && $stock !== '') {
                    $sql      .= " AND stock = ?";
                    $params[]  = (int)$stock;
                    $types    .= 'i';
                }
                $tipo = isset($_GET['tipo']) ? strtolower(trim($_GET['tipo'])) : '';  
                if ($tipo !== '') {
                    $sql      .= " AND LOWER(tipo) = ?";
                    $params[]  = $tipo;
                    $types    .= 's';
                }

                if ($marca !== null && $marca !== '') {
                    $sql      .= " AND marca = ?";
                    $params[]  = $marca;
                    $types    .= 's';
                }

                $sql .= " ORDER BY id DESC";

                if ($params) {
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param($types, ...$params);
                    $stmt->execute();
                    $result = $stmt->get_result();
                } else {
                    $result = mysqli_query($conn, $sql);
                }

                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<tr id="linha-' . $row['id'] . '" data-id="' . $row['id'] . '">';
                        echo '<td>' . $row['id'] . '</td>';
                        echo '<td>' . htmlspecialchars($row['nome']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['tipo']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['marca']) . '</td>';
                        echo '<td>' . number_format($row['preco'], 2, ',', '.') . '</td>';
                        echo '<td>' . $row['stock'] . '</td>';
                        echo '<td><img src="../imagens/' . htmlspecialchars($row['caminho_arquivo']) . '" width="60" alt="Imagem do componente"></td>';
                        echo '<td class="acoes">';
                        echo '  <a href="../admin_gestao/editar_componentes.php?id=' . $row['id'] . '&' . $queryFiltros . '#linha-' . $row['id'] . '" class="btn editar">Editar</a>';
                        '<br>';
                        echo '  <button class="btn remover" data-id="' . $row['id'] . '">Apagar</button>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="8">Nenhum componente encontrado.</td></tr>';
                }
                ?>
                </tbody>
            </table>
            <a href="../admin/admin_dashboard.php" class="btn voltar" style="margin-top:15px;">Voltar</a>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $('.btn-remover').click(function(){
        const botao = $(this);
        const id = botao.data('id');

        if(confirm('Tem certeza que quer remover este componente?')) {
            $.post('../admin_gestao/remover_componente.php', { id: id }, function(resposta){
                if(resposta.trim() === 'ok'){
                    botao.closest('tr').remove();
                } else {
                    alert('Erro ao remover o componente.');
                }
            });
        }
    });
});
</script>

</body>
</html>
