<?php
include_once '../pagcabecs/cabec.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($utilizador)) {
    $utilizador = ['id' => 0]; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="../css/naveindex.css">
     <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Amiko:wght@600&display=swap" rel="stylesheet">
    <title>Suporte</title>
</head>
<body>

    <div class="bg">
    <div class="overlay"></div>
    <div class="content">

<h1 align="center" class="amiko-semibold">Suporte ao Cliente</h1>
<br>
<p><b>PT</b></p>
<br>
<p align="center" class="amiko-semibold">Se precisar de ajuda ou tiver alguma dúvida, não hesite em nos contactar!</p>
<br>
<p align="center" class="amiko-semibold">
Contacte-nos através do email: <i>pcmastergeral@gmail.com</i>
</p>
<br>
<p align="center" class="amiko-semibold">Ou ligue para o nosso número de suporte: +351 926 133 282 / +351 912 025 261</p>
<br>
<p align="center" class="amiko-semibold">Estamos disponíveis de segunda a sexta, das 9h às 18h.</p>
<br>
<p align="center" class="amiko-semibold">Agradecemos o seu contato!</p>

<h1 align="center" class="amiko-semibold">Customer Support</h1>
<br>
<p><b>ENG</b></p>
<br>
<p align="center" class="amiko-semibold">If you need help or have any questions, please don't hesitate to contact us!</p>
<br>
<p align="center" class="amiko-semibold">
Contact us by email: <i>pcmastergeral@gmail.com</i>
</p>
<br>
<p align="center" class="amiko-semibold">Or call our support number: +351 926 133 282 / +351 912 025 261</p>
<br>
<p align="center" class="amiko-semibold">We are available Monday to Friday, from 9 am to 6 pm.</p>
<br>
<p align="center" class="amiko-semibold">Thank you for contacting us!</p>
</div></div>
</body>
</html>