<?php
include_once 'cabecgestao.php';
include_once '../db.php';

$erro     = '';
$sucesso  = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $erro = 'ID inválido.';
} else {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT * FROM utilizadores WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $erro = 'Utilizador não encontrado.';
    } else {
        $utilizador = $result->fetch_assoc();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $nome     = trim($_POST['nome']);
            $email    = trim($_POST['email']);
            $numtel   = trim($_POST['numtel']);
            $senhaNova= trim($_POST['password']);
            $tipo     = $_POST['tipo'];  // Novo campo de tipo

            if (empty($nome) || empty($email) || empty($numtel) || empty($tipo)) {
                $erro = 'Nome, e-mail, telefone e tipo são obrigatórios.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erro = 'E-mail inválido.';
            } elseif (!preg_match('/^\d{9}$/', $numtel)) {
                $erro = 'Telefone inválido (9 dígitos).';
            } else {

                if ($senhaNova !== '') {
                    $hash = password_hash($senhaNova, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("
                        UPDATE utilizadores
                           SET nome = ?, email = ?, numtel = ?, password = ?, tipo = ?
                         WHERE id = ?
                    ");
                    $stmt->bind_param("sssssi", $nome, $email, $numtel, $hash, $tipo, $id);
                } else {
                    $stmt = $conn->prepare("
                        UPDATE utilizadores
                           SET nome = ?, email = ?, numtel = ?, tipo = ?
                         WHERE id = ?
                    ");
                    $stmt->bind_param("ssssi", $nome, $email, $numtel, $tipo, $id);
                }

                if ($stmt->execute()) {
                    $sucesso    = 'Utilizador atualizado com sucesso!';
                    $utilizador['nome']   = $nome;
                    $utilizador['email']  = $email;
                    $utilizador['numtel'] = $numtel;
                    $utilizador['tipo']   = $tipo;  // Atualizando tipo
                    if ($senhaNova !== '') {
                        $utilizador['password'] = $hash;
                    }
                } else {
                    $erro = 'Erro ao atualizar utilizador: ' . $stmt->error;
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
  <title>Editar Utilizador</title>
  <link rel="stylesheet" href="../css/admin_criar.css">
</head>
<body>
<div class="bg">
 <div class="overlay"></div>
 <div class="content">
  <form method="POST">
    <h2>Editar Utilizador</h2>

    <?php if ($erro): ?>
      <p class="error-message"><?= htmlspecialchars($erro) ?></p>
    <?php elseif ($sucesso): ?>
      <p class="success-message"><?= htmlspecialchars($sucesso) ?></p>
    <?php endif; ?>

    <?php if (isset($utilizador)): ?>
      <label>Nome:</label>
      <input type="text" name="nome" value="<?= htmlspecialchars($utilizador['nome']) ?>" required>

      <label>E-mail:</label>
      <input type="email" name="email" value="<?= htmlspecialchars($utilizador['email']) ?>" required>

      <label>Telefone (9 dígitos):</label>
      <input type="text" name="numtel" value="<?= htmlspecialchars($utilizador['numtel']) ?>" required pattern="\d{9}" title="Introduza 9 dígitos.">

      <label>Nova password:<br><small>(Deixe em branco para manter a atual)</small></label>
      <input type="password" name="password" minlength="6">

      <label>Tipo:</label>
      <select name="tipo" required>
        <option value="utilizador" <?= $utilizador['tipo'] === 'utilizador' ? 'selected' : '' ?>>Utilizador</option>
        <option value="admin" <?= $utilizador['tipo'] === 'admin' ? 'selected' : '' ?>>Admin</option>
      </select>

      <button type="submit">Guardar Alterações</button>
      <a href="../admin/admin_utilizadores.php" class="btn voltar" style="margin-left: 10px;">Voltar</a>
    <?php endif; ?>
  </form>
 </div>
</div>
</body>
</html>
