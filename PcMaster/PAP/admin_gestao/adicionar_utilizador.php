<?php
include_once 'cabecgestao.php'; 
include_once '../db.php';  

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome     = trim($_POST['nome']);
    $email    = trim($_POST['email']);
    $numtel   = trim($_POST['numtel']);
    $password = $_POST['password'];
    $tipo     = $_POST['tipo'];

    if (empty($nome) || empty($email) || empty($numtel) || empty($password) || empty($tipo)) {
        $erro = 'Todos os campos são obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail inválido.';
    } elseif (!preg_match('/^\d{9}$/', $numtel)) { 
        $erro = 'Número de telefone inválido.';
    } else {

        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Agora, a query vai inserir também o campo 'tipo'
        $stmt = $conn->prepare(
            "INSERT INTO utilizadores (nome, email, numtel, password, tipo) VALUES (?, ?, ?, ?, ?)"
        );

        if ($stmt) {
            // Bindando os parâmetros da query incluindo o tipo
            $stmt->bind_param("sssss", $nome, $email, $numtel, $hash, $tipo);

            if ($stmt->execute()) {
                $sucesso = 'Utilizador adicionado com sucesso!';
            } else {
                $erro = 'Erro ao adicionar utilizador: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $erro = 'Erro na preparação da query: ' . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Utilizador</title>
    <link rel="stylesheet" href="../css/admin_criar.css">
</head>
<body>
<div class="bg">
  <div class="overlay"></div>
    <div class="content">

      <form method="POST">
        <h2>Adicionar Utilizador</h2>

        <?php if ($erro): ?>
          <p class="error-message"><?= htmlspecialchars($erro) ?></p>
        <?php elseif ($sucesso): ?>
          <p style="color: green; font-weight: bold;"><?= htmlspecialchars($sucesso) ?></p>
        <?php endif; ?>

        <label for="nome">Nome:</label>
        <input type="text" name="nome" value="<?= isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : '' ?>" required>

        <label for="email">E-mail:</label>
        <input type="email" name="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>

        <label for="numtel">Telefone (9 dígitos):</label>
        <input type="text" name="numtel" value="<?= isset($_POST['numtel']) ? htmlspecialchars($_POST['numtel']) : '' ?>" required pattern="\d{9}" title="Introduza 9 dígitos.">

        <label for="password">Password:</label>
        <input type="password" name="password" required minlength="6">
        
        <label for="tipo">Tipo:</label>
        <select name="tipo" required>
            <option value="utilizador" <?= isset($_POST['tipo']) && $_POST['tipo'] === 'utilizador' ? 'selected' : '' ?>>Utilizador</option>
            <option value="admin" <?= isset($_POST['tipo']) && $_POST['tipo'] === 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>

        <button type="submit">Adicionar</button>
        <a href="../admin/admin_utilizadores.php" class="btn voltar" style="margin-left: 10px;">Voltar</a>
      </form>

    </div>
  </div>
</div>
</body>
</html>
