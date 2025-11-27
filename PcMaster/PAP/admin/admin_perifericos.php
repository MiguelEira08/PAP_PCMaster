<?php
include_once 'cabecadm.php';
include_once '../db.php';

$stock = filter_input(INPUT_GET, 'stock', FILTER_SANITIZE_NUMBER_INT);
$tipo  = filter_input(INPUT_GET, 'tipo', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
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
    <title>Painel de Administração - Periféricos</title>
    <link rel="stylesheet" href="../css/admin_produto.css">
    <link rel="icon" type="image/png" href="../imagens/icon.png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="bg">
    <div class="overlay"></div>
    <br><br><br>
    <div class="content">
        <h2>Gestão de Periféricos</h2>

        <form method="GET" class="form-filtros">
            <label for="stock">Stock:</label>
            <select name="stock" id="stock">
                <option value="">Todos</option>
                <?php
                $resStock = mysqli_query($conn, "SELECT DISTINCT stock FROM perifericos ORDER BY stock ASC");
                while ($s = mysqli_fetch_assoc($resStock)) {
                    $val = $s['stock'];
                    $sel = ($stock !== null && $stock !== '' && (string)$val === (string)$stock) ? 'selected' : '';
                    echo '<option value="' . htmlspecialchars($val) . '" ' . $sel . '>' . htmlspecialchars($val) . '</option>';
                }
                ?>
            </select>

            <label for="tipo">Tipo:</label>
            <select name="tipo" id="tipo">
                <option value="">Todos</option>
                <?php
                $resTipos = mysqli_query($conn, "SELECT DISTINCT tipo FROM perifericos ORDER BY tipo ASC");
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
                $resMarcas = mysqli_query($conn, "SELECT DISTINCT marca FROM perifericos ORDER BY marca ASC");
                while ($m = mysqli_fetch_assoc($resMarcas)) {
                    $sel = ($marca !== null && $marca !== '' && $m['marca'] === $marca) ? 'selected' : '';
                    echo '<option value="' . htmlspecialchars($m['marca']) . '" ' . $sel . '>' . htmlspecialchars($m['marca']) . '</option>';
                }
                ?>
            </select>

            <button type="submit">Procurar</button>
            <a href="../admin_gestao/adicionar_perifericos.php" class="btn criar" style="margin-left:10px;">Adicionar Periféricos</a>
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
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $sql    = "SELECT * FROM perifericos WHERE 1=1";
                $params = [];
                $types  = '';

                if ($stock !== null && $stock !== '') {
                    $sql     .= " AND stock = ?";
                    $params[]  = (int)$stock;
                    $types    .= 'i';
                }

                if ($tipo !== null && $tipo !== '') {
                    $sql     .= " AND LOWER(tipo) LIKE LOWER(CONCAT('%', ?, '%'))";
                    $params[]  = $tipo;
                    $types    .= 's';
                }

                if ($marca !== null && $marca !== '') {
                    $sql     .= " AND marca = ?";
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
                        echo '<tr id="linha-' . $row['id'] . '">';
                        echo '<td>' . $row['id'] . '</td>';
                        echo '<td>' . htmlspecialchars($row['nome']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['tipo']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['marca']) . '</td>';
                        echo '<td>' . number_format($row['preco'], 2, ',', '.') . '</td>';
                        echo '<td>' . $row['stock'] . '</td>';
                        echo '<td><img src="../imagens/' . htmlspecialchars($row['caminho_arquivo']) . '" width="60" alt="Imagem do periférico"></td>';
                        echo '<td class="acoes">';
                        echo '  <a href="../admin_gestao/editar_perifericos.php?id=' . $row['id'] . '&' . $queryFiltros . '" class="btn editar">Editar</a>';
                        echo '  <a href="#" class="btn remover" onclick="removerPeriferico(' . $row['id'] . '); return false;">Apagar</a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="8">Nenhum periférico encontrado.</td></tr>';
                }
                ?>
                </tbody>
            </table>
            <a href="../admin/admin_dashboard.php" class="btn voltar" style="margin-top:15px;">Voltar</a>
        </div>
    </div>
</div>

<script>
function removerPeriferico(id) {
    if (confirm('Tem a certeza que deseja remover este periférico?')) {
        $.ajax({
            url: '../admin_gestao/remover_periferico.php',
            type: 'POST',
            data: { id: id },
            success: function(response) {
                if (response.trim() === 'ok') {
                    $('#linha-' + id).fadeOut();
                } else {
                    alert('Erro ao remover periférico.');
                }
            },
            error: function() {
                alert('Erro na requisição.');
            }
        });
    }
}
</script>

</body>
</html>
