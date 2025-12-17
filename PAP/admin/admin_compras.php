<?php
include_once 'cabecadm.php';
include_once '../db.php';

$userId  = filter_input(INPUT_GET, 'user_id', FILTER_SANITIZE_NUMBER_INT);
$estadoF = filter_input(INPUT_GET, 'estado',  FILTER_SANITIZE_FULL_SPECIAL_CHARS);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Painel de Administração – Compras</title>
    <link rel="stylesheet" href="../css/admin_produto.css">
    <link rel="icon" type="image/png" href="../imagens/icon.png">
</head>
<body>
<div class="bg">
    <div class="overlay"></div>
    <br><br><br>
    <div class="content">   
        <table class="admin-table">
            <thead>
                <tr><th colspan="8"><h2>Compras por Utilizador</h2></th></tr>
                <tr>
                    <td colspan="8">
                        <form method="GET" class="form-filtros">
                            <label for="user_id">Utilizador:</label>
                            <select name="user_id" id="user_id">
                                <option value="">Todos</option>
                                <?php
                                $resUsers = mysqli_query($conn, "SELECT DISTINCT u.id, u.nome
                                                                 FROM utilizadores u
                                                                 JOIN fim_compra fc ON fc.utilizador_id = u.id
                                                                 ORDER BY u.nome ASC");
                                while ($u = mysqli_fetch_assoc($resUsers)) {
                                    $sel = ($userId !== null && $userId !== '' && (int)$u['id'] === (int)$userId) ? 'selected' : '';
                                    echo '<option value="' . $u['id'] . '" ' . $sel . '>' . htmlspecialchars($u['nome']) . '</option>';
                                }
                                ?>
                            </select>
                            <label for="estado">Estado:</label>
                            <select name="estado" id="estado">
                                <?php
                                $estados = ['' => 'Todos', 'Pendente' => 'Pendente', 'A caminho' => 'A caminho', 'Entregue' => 'Entregue'];
                                foreach ($estados as $val => $label) {
                                    $sel = ($estadoF !== null && (string)$estadoF === (string)$val && $val !== '') ? 'selected' : '';
                                    echo '<option value="' . $val . '" ' . $sel . '>' . $label . '</option>';
                                }
                                ?>
                            </select>
                            <button type="submit">Filtrar</button>
                            <a href="../admin/admin_dashboard.php" class="btn voltar" style="margin-left:10px;">Voltar</a>
                        </form>
                    </td>
                </tr>
                <tr>
                    <th>Utilizador</th>
                    <th>Email</th>
                    <th>ID Compra</th>
                    <th>Data</th>
                    <th>Total (€)</th>
                    <th>Estado</th>
                    <th>Detalhes</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $sql = "SELECT fc.id AS compra_id,
                           fc.data_compra,
                           fc.estado,
                           u.id   AS user_id,
                           u.nome AS user_nome,
                           u.email AS user_email,
                           COALESCE(SUM(fci.preco * fci.quantidade), 0) AS total
                    FROM fim_compra fc
                    JOIN utilizadores u ON u.id = fc.utilizador_id
                    LEFT JOIN fim_compra_itens fci ON fci.compra_id = fc.id
                    WHERE 1 = 1";
            $params = [];
            $types  = '';

            if ($userId !== null && $userId !== '') {
                $sql     .= " AND u.id = ?";
                $params[]  = (int)$userId;
                $types    .= 'i';
            }
            if ($estadoF !== null && $estadoF !== '') {
                $sql     .= " AND fc.estado = ?";
                $params[]  = $estadoF;
                $types    .= 's';
            }

            $sql .= " GROUP BY fc.id
                      ORDER BY u.nome ASC, fc.data_compra DESC";

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
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['user_nome'])  . '</td>';
                    echo '<td>' . htmlspecialchars($row['user_email']) . '</td>';
                    echo '<td>' . $row['compra_id'] . '</td>';
                    echo '<td>' . date('d/m/Y H:i', strtotime($row['data_compra'])) . '</td>';
                    echo '<td>' . number_format($row['total'], 2, ',', '.') . '</td>';
                    echo '<td>' . htmlspecialchars($row['estado']) . '</td>';
                    echo '<td><a class="btn editar" href="detalhes_compra.php?id=' . $row['compra_id'] . '">Ver</a></td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="7">Nenhuma compra encontrada.</td></tr>';
            }
            ?>
            </tbody>
        </table>
            <a href="admin_dashboard.php" class="btn voltar">Voltar</a>
    </div>
</div>
</body>
</html>
