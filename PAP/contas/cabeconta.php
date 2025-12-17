<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./css/naveindex.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Alumni+Sans+Pinstripe&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="../imagens/icon.png">
</head>
<body>
<header>
    <nav >
        <img src="../imagens/logo.png" class="logo">
        <ul>
            <a href="../index/index.php">A nossa página</a>
            <a href="../comprar/loja.php">Compre Aqui</a>
            <a href="../comparar/comparar.php">Compare Aqui</a>
            <a href="../carrinho/carrinho.php">O meu Carrinho</a>
            <?php if (!isset($_SESSION['nome'])): ?>
                <a href="../login/login.php">Login</a>
            <?php else: ?>
                <a href="../contas/conta.php">A minha conta</a>
                <a href="../login/logout.php" onclick="return confirm('Vai voltar á página inicial, tem certeza que quer dar logout?');">Logout</a>
            <?php endif; ?>
        </ul>
    </nav>
</header>
</body>
</html>
