<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/db.php";

$tipoSessao = $_SESSION['tipo'] ?? 'guest';

$fotoPerfil = "./imagens/user.png"; // imagem padrão

if (isset($_SESSION['user_id'])) {
    $idUser = $_SESSION['user_id'];

    $sqlFoto = $conn->query("SELECT caminho_arquivo FROM utilizadores WHERE id = $idUser");
    $dadosFoto = $sqlFoto->fetch_assoc();

    if (!empty($dadosFoto['caminho_arquivo'])) {
        $fotoPerfil = "../" . ltrim($dadosFoto['caminho_arquivo'], "/");
    }
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
    <link rel="icon" type="image/png" href="./imagens/icon.png">

    <style>
        .foto-perfil-menu {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            vertical-align: middle;
        }
       .foto-perfil-menu:hover {
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.7);
            transition: box-shadow 0.3s ease;
            border: 2px solid #ff9100;
        } 

        .perfil-link {
            display: flex;
            align-items: center;
        }

        .icon-carrinho {
            width: 35px;
            height: 35px;
            object-fit: contain;
            vertical-align: middle;
        }
        .menu-user {
    position: relative; /* MUITO IMPORTANTE */
    display: inline-block;
}

.user-dropdown {
    position: absolute;
    top: 55px;          /* aparece por baixo da imagem */
    right: 0;
    background: #ffffffc0;
    border-radius: 8px;
    min-width: 180px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    display: none;
    z-index: 9999;
    
}

.user-dropdown a {
    border: 2px solid;
    border-radius: 75px;
    display: block;
    padding: 10px 15px;
    text-decoration: none;
    color: #333;
    font-size: 14px;
}

.user-dropdown a:hover {
    background: #f2f2f2;
    border: 2px solid #ff9100;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.7);
    transition: box-shadow 0.3s ease;
}

.user-dropdown .logout {
    color: #c0392b;
}

    </style>
</head>

<body>

<header>
<nav>
    <img src="../imagens/logo.png" class="logo">

    <ul class="menu-links">
    <?php
    $sql = $conn->query("SELECT * FROM menu WHERE ativo = 1 ORDER BY ordem ASC");

    while ($row = $sql->fetch_assoc()) {

        $tipoMenu = $row['tipo'];
        $mostrar = false;

        if ($tipoMenu === 'todos') {
            $mostrar = true;
        }

        elseif ($tipoMenu === 'guest' && $tipoSessao === 'guest') {
            $mostrar = true;
        }

        elseif ($tipoMenu === 'utilizador' && $tipoSessao === 'utilizador') {
            $mostrar = true;
        }

        elseif ($tipoMenu === 'admin' && $tipoSessao === 'admin') {
            $mostrar = true;
        }

        if ($mostrar) {

if ($row['Nome'] === 'Conta') {

    $nomeExibir = isset($_SESSION['nome']) ? htmlspecialchars($_SESSION['nome']) : 'Utilizador';

    echo '
    <div class="menu-user">
        <img src="'.$fotoPerfil.'" class="foto-perfil-menu" id="userAvatar" style="cursor:pointer;">

        <div class="user-dropdown" id="userDropdown">
            <div style="padding: 5px 5px; border-bottom: 1px solid #eeeeee00;">
                <span style="display:block; font-weight: bold; color: #333;">Olá, ' . $nomeExibir . '!</span>
            </div>
            <a href="'.BASE_URL.'./contas/conta.php">Gerir perfil</a>
            <a href="'.BASE_URL.'./login/logout.php" class="logout">Logout</a>
        </div>
    </div>';
}


            elseif ($row['Nome'] === 'Carrinho') {
                echo '
                <a href="'.BASE_URL.'/'.$row['link'].'">
                    <img src="../imagens/compra.png" class="icon-carrinho">
                </a>';
            }

            else {
                echo '<a href="'.BASE_URL.'/'.$row['link'].'">'.$row['Nome'].'</a>';
            }
        }
    }
    ?>
    </ul>
</nav>
</header>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const avatar = document.getElementById("userAvatar");
    const dropdown = document.getElementById("userDropdown");

    avatar.addEventListener("click", function (e) {
        e.stopPropagation();
        dropdown.style.display =
            dropdown.style.display === "block" ? "none" : "block";
    });

    document.addEventListener("click", function () {
        dropdown.style.display = "none";
    });
});
</script>

</body>
</html>
