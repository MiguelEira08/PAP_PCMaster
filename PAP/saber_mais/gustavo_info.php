</body>
</html>
<?php
session_start();
include_once __DIR__ . '/../db.php';
include_once __DIR__ . '/../cabecindex.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Equipa | PcMaster</title>
    <link rel="stylesheet" href="../css/info_equipa.css">
</head>

<body>
<main class="bg">
    <div class="overlay"></div>
    <section class="content">
        <div class="hero-container">
                <div class="hero-image">
                    <img src="../imagens/Gustavo1.webp" alt="Foto de perfil">
                </div>
            <div class="hero-text">
                <h1>Olá!</h1>
                <h2>O meu nome é <br><span>Gustavo Figueiredo</span></h2>
            </div>
        </div>
        <br><br>
        <h3 align="left"><b>Gustavo Figueiredo</b> é um dos criadores do projeto PcMaster.<br> Este, está encarregue da comparação dos produtos que são vendidos<br>Nasceu a 3 de Dezembro de 2008, atualmente tem 17 anos e vive na Branca.<br>Gustavo estudou o 1º ciclo inteiro na Escola Primária das Laginhas, seguiu os 2º e 3º ciclos na Escola Básica de Branca, no curso regular.<br>Finalmente, frequentou a Escola Secundária de Albergaria-A-Velha, concluindo o 12º ano com 20 na PAP.</h3> 
        <br><center>
  <div class="caixa-container">
  <div class="botao-link"  onclick="window.location.href='./sobre_nos.php';">Voltar atrás</div>
            </center>
    </section>
</main>
</body>
</html>
