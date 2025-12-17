<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/admin_dash.css">
    <link rel="icon" type="image/png" href="../imagens/icon.png">
</head>
<body>
<header>
    <nav>
        <img src="../imagens/logo.png" class="logo">
        <ul>
            <a href="admin_dashboard.php">Página Inicial</a>
            <a href="admin_componentes.php">Componentes</a>
            <a href="admin_perifericos.php">Periféricos</a>
            <a href="admin_utilizadores.php">Utilizadores</a>
            <a href="feedback_cliente.php">Feedbacks</a>
            <a href="admin_compras.php">Compras Realizadas</a>
            <a href="../login/logout.php" onclick="return confirm('Vai voltar á página inicial, tem certeza que quer dar logout?');">Logout</a>

        </ul>
    </nav>
</header>
</body>
</html>
