<?php
session_start();
include_once __DIR__ . '/../db.php';
include_once __DIR__ . '/../cabecindex.php';
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../index/index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Painel de Administração - Utilizadores & Administradores</title>
    <link rel="stylesheet" href="../css/admin_produto.css">
    <link rel="icon" type="image/png" href="../imagens/icon.png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.6/css/dataTables.dataTables.css" />
</head>
<body>
<div class="bg">
 <div class="overlay"></div>
  <br><br><br>
  <div class="content">

    <h2>Gestão de Contas</h2>
    <a href="../admin_gestao/adicionar_utilizador.php" class="btn criar" style="margin-bottom:15px;">Adicionar Conta</a>

<div class="table-container">
   <table  id="tabela" class="datatable">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Email</th>
        <th>Telefone</th>     
        <th>tipo</th>
        <th>Estado</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $resultAdminsTipo = mysqli_query($conn, "
    SELECT u.*, us.bloqueado
    FROM utilizadores u
    LEFT JOIN utilizador_seguranca us 
        ON u.id = us.utilizador_id
    WHERE u.tipo IN ('admin', 'utilizador')
    ORDER BY u.id DESC
    ");
    if ($resultAdminsTipo && mysqli_num_rows($resultAdminsTipo) > 0) {
    while ($row = mysqli_fetch_assoc($resultAdminsTipo)) {

        $estado = ($row['bloqueado'] === 'sim')
            ? '<span>Bloqueado</span>'
            : '<span>Ativo</span>';

        echo '<tr>';
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['nome']}</td>";
        echo "<td>{$row['email']}</td>";
        echo "<td>{$row['numtel']}</td>";
        echo "<td>{$row['tipo']}</td>";
        echo "<td>{$estado}</td>";
        echo '<td class="acoes">';
 if ($row['tipo'] === 'admin') {
            echo '<button class="btn remover" onclick="removerAdmin(' . $row['id'] . ', this)">Apagar</button>';
        } else {
            echo '<button class="btn remover" onclick="removerUtilizador(' . $row['id'] . ', this)">Apagar</button>';
        }

        echo ' <a href="../admin_gestao/editar_utilizador.php?id=' . $row['id'] . '" class="btn editar">Editar</a>';
        echo '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="7">Nenhum utilizador encontrado.</td></tr>';
}
    '</td>';
    ?>
    </tbody>
  </table>
        <center>
            <a href="../admin/admin_dashboard.php" class="btn voltar" style="margin-top:15px;">Voltar á Dashboard</a>
        </center>

</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/2.3.6/js/dataTables.js"></script>
<script src="scriptadmin.js"></script>
<script>
  function removerAdmin(id, btn) {
    if (confirm('Tem a certeza que deseja remover este administrador?')) {
      fetch('../admin_gestao/remover_admin.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + id
      })
      .then(response => response.text())
      .then(text => {
        if (text.trim() === 'ok') {
          let table = $(btn).closest('table').DataTable();
table.row($(btn).parents('tr')).remove().draw();
        } else {
          alert(text);
        }
      })
      .catch(() => alert('Erro na remoção.'));
    }
  }

  function removerUtilizador(id, btn) {
    if (confirm('Tem a certeza que deseja remover este utilizador?')) {
      fetch('../admin_gestao/remover_utilizador.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + id
      })
      .then(response => response.text())
      .then(text => {
        if (text.trim() === 'ok') {
          let table = $(btn).closest('table').DataTable();
table.row($(btn).parents('tr')).remove().draw();
        } else {
          alert(text);
        }
      })
      .catch(() => alert('Erro na remoção.'));
    }
  }
</script>

</body>
</html>
