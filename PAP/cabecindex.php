<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/db.php";
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
</head>
<body>

<header>
<nav>
    <img src="../imagens/logo.png" class="logo">

    <ul class="menu-links">
    <?php
    $sql = $conn->query("SELECT * FROM menu WHERE ativo = 1 ORDER BY id_menu ASC");

    while ($row = $sql->fetch_assoc()) {

        $tipoMenu = $row['tipo'];
        $mostrar = false;

        // todos
        if ($tipoMenu === 'todos') {
            $mostrar = true;
        }

        // visitante
        elseif ($tipoMenu === 'guest' && !isset($_SESSION['nome'])) {
            $mostrar = true;
        }

        // utilizador logado
        elseif (
            $tipoMenu === 'utilizador' &&
            isset($_SESSION['nome']) &&
            in_array($_SESSION['tipo'], ['utilizador', 'admin'])
        ) {
            $mostrar = true;
        }

        // admin
        elseif (
            $tipoMenu === 'admin' &&
            isset($_SESSION['nome']) &&
            $_SESSION['tipo'] === 'admin'
        ) {
            $mostrar = true;
        }

        if ($mostrar) {
            echo '<a href="'.BASE_URL.'/'.$row['link'].'">'.$row['Nome'].'</a>';
        }
    }
    ?>
    </ul>
</nav>
</header>
</body>
</html>

