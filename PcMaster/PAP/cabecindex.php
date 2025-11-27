<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "db.php"; 
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./css/naveindex.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alumni+Sans+Pinstripe&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="./imagens/icon.png">
    <title>Navegação</title>
</head>
<body>
<header>
    <nav>
        <img src="./imagens/logo.png" class="logo">

        <ul>
            <?php
            
            $sql = $conn->query("SELECT * FROM menu ORDER BY id_menu ASC");

            while ($row = $sql->fetch_assoc()):
            ?>
                <a href="<?php echo $row['link']; ?>">
                    <?php echo $row['Nome']; ?>
                </a>
            <?php endwhile; ?>

            <!-- LOGIN / LOGOUT -->
            <?php if (!isset($_SESSION['nome'])): ?>
                <a href="./login/login.php">Login</a>
            <?php else: ?>
                <a href="./contas/conta.php">Olá, <?php echo $_SESSION['nome']; ?></a>
                <a href="./login/logout.php"
                   onclick="return confirm('Vai voltar à página inicial, tem certeza que quer dar logout?');">
                   Logout
                </a>
            <?php endif; ?>
        </ul>

    </nav>
</header>
</body>
</html>
