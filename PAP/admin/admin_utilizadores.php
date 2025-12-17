<?php
include_once 'cabecadm.php';
include_once '../db.php';
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Painel de Administração - Utilizadores & Administradores</title>
    <link rel="stylesheet" href="../css/admin_produto.css">
    <link rel="icon" type="image/png" href="../imagens/icon.png">
</head>
<body>
<div class="bg">
 <div class="overlay"></div>
  <br><br><br>
  <div class="content">

    <h2>Gestão de Administradores</h2>

    <a href="../admin_gestao/adicionar_utilizador.php" class="btn criar" style="margin-bottom:15px;">
    Adicionar Conta
    </a>
    <a href="../admin/admin_dashboard.php" class="btn voltar" style="margin-left:10px;">Voltar</a>
    
<div class="table-container">
  <table class="admin-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Email</th>
        <th>Telefone</th>
        <th>tipo</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
    <?php
      $resultAdminsTipo = mysqli_query($conn, "SELECT * FROM utilizadores WHERE tipo = 'admin' ORDER BY id DESC");
      if ($resultAdminsTipo && mysqli_num_rows($resultAdminsTipo) > 0) {
          while ($row = mysqli_fetch_assoc($resultAdminsTipo)) {
              echo '<tr>';
              echo "<td>{$row['id']}</td>";
              echo "<td>{$row['nome']}</td>";
              echo "<td>{$row['email']}</td>";
              echo "<td>{$row['numtel']}</td>";
              echo "<td>{$row['tipo']}</td>";
              echo '<td class="acoes">
                          <button class="btn remover" onclick="removerUtilizador(' . $row['id'] . ', this)">Apagar</button>
                          <a href="../admin_gestao/editar_utilizador.php?id='.$row['id'].'" class="btn editar">Editar</a>
                    </td>';
              echo '</tr>';
          }
      } else {
          echo '<tr><td colspan="6">Nenhum administrador (tipo admin) encontrado.</td></tr>';
      }
    ?>
        </tbody>
      </table>

    <hr style="margin:40px 0; opacity:.4;">
<h2>Gestão de Utilizadores</h2>
<div class="table-container">
  <table class="admin-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Email</th>
        <th>Telefone</th>
        <th>tipo</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
    <?php
      $resultUsers = mysqli_query($conn, "SELECT * FROM utilizadores WHERE tipo = 'utilizador' ORDER BY id DESC");
      if ($resultUsers && mysqli_num_rows($resultUsers) > 0) {
          while ($row = mysqli_fetch_assoc($resultUsers)) {
              echo '<tr>';
              echo "<td>{$row['id']}</td>";
              echo "<td>{$row['nome']}</td>";
              echo "<td>{$row['email']}</td>";
              echo "<td>{$row['numtel']}</td>";
              echo "<td>{$row['tipo']}</td>";
              echo '<td class="acoes">
                          <button class="btn remover" onclick="removerUtilizador(' . $row['id'] . ', this)">Apagar</button>
                          <a href="../admin_gestao/editar_utilizador.php?id='.$row['id'].'" class="btn editar">Editar</a>
                    </td>';
              echo '</tr>';
          }
      } else {
          echo '<tr><td colspan="6">Nenhum utilizador encontrado.</td></tr>';
      }
    ?>
    </tbody>
  </table>
</div>

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
          btn.closest('tr').remove();
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
          btn.closest('tr').remove();
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
