<?php
include_once '../pagcabecs/cabec.php';
include_once '../db.php';
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>Loja Periféricos</title>
  <link rel="stylesheet" href="../css/comprar.css">
  <link rel="icon" type="image/png" href="../imagens/icon.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="bg">
  <div class="loja-container">
  <div class="overlay"></div>
    <aside class="sidebar">
      <ul>
        <li><a href="perifericos_lista.php">Todos os Tipos</a></li>
        <li class="has-sub"><a href="#">Fones</a>
          <ul class="sub-menu">
            <li><a href="?tipo=Fones">Todos</a></li>
            <li><a href="?tipo=Fones&q=Asus">Asus</a></li>
            <li><a href="?tipo=Fones&q=Corsair">Corsair</a></li>
            <li><a href="?tipo=Fones&q=HyperX">HyperX</a></li>
            <li><a href="?tipo=Fones&q=Logitech">Logitech</a></li>
            <li><a href="?tipo=Fones&q=Mars Gaming">Mars Gaming</a></li>
            <li><a href="?tipo=Fones&q=Razer">Razer</a></li>

          </ul>
        </li>
        <li class="has-sub"><a href="#">Teclados</a>
          <ul class="sub-menu">
            <li><a href="?tipo=Teclado">Todos</a></li>
            <li><a href="?tipo=Teclado&q=Asus">Asus</a></li>
            <li><a href="?tipo=Teclado&q=Corsair">Corsair</a></li>
            <li><a href="?tipo=Teclado&q=Gigabyte">Gigabyte</a></li>
            <li><a href="?tipo=Teclado&q=HyperX">HyperX</a></li>
            <li><a href="?tipo=Teclado&q=Logitech">Logitech</a></li>
            <li><a href="?tipo=Teclado&q=Mars Gaming">Mars Gaming</a></li>
            <li><a href="?tipo=Teclado&q=Razer">Razer</a></li>
            <li><a href="?tipo=Teclado&q=SteelSeries">SteelSeries</a></li>
          </ul>
        </li>
        <li class="has-sub"><a href="#">Ratos</a>
          <ul class="sub-menu">
            <li><a href="?tipo=Rato">Todos</a></li>
            <li><a href="?tipo=Rato&q=Asus">Asus</a></li>
            <li><a href="?tipo=Rato&q=Corsair">Corsair</a></li>
            <li><a href="?tipo=Rato&q=Gigabyte">Gigabyte</a></li>
            <li><a href="?tipo=Rato&q=HyperX">HyperX</a></li>
            <li><a href="?tipo=Rato&q=Mars Gaming">Mars Gaming</a></li>
            <li><a href="?tipo=Rato&q=NPlay">NPlay</a></li>
            <li><a href="?tipo=Rato&q=Razer">Razer</a></li>
            
          </ul>
        </li>
        <li class="has-sub"><a href="#">Tapetes de Rato</a>
          <ul class="sub-menu">
            <li><a href="?tipo=Tapete">Todos</a></li>
            <li><a href="?tipo=Tapete&q=Asus">Asus</a></li>
            <li><a href="?tipo=Tapete&q=Corsair">Corsair</a></li>
            <li><a href="?tipo=Tapete&q=Logitech">Logitech</a></li>
            <li><a href="?tipo=Tapete&q=Mars Gaming">Mars Gaming</a></li>
            <li><a href="?tipo=Tapete&q=Razer">Razer</a></li>
          </ul>
        </li>
        <li class="has-sub"><a href="#">Monitores</a>
          <ul class="sub-menu">
            <li><a href="?tipo=Monitor">Todos</a></li>
            <li><a href="?tipo=Monitor&q=Asus">Asus</a></li>
            <li><a href="?tipo=Monitor&q=AOC">AOC</a></li>
            <li><a href="?tipo=Monitor&q=Acer">Acer</a></li>
            <li><a href="?tipo=Monitor&q=BenQ">BenQ</a></li>
            <li><a href="?tipo=Monitor&q=Gigabyte">Gigabyte</a></li>
            <li><a href="?tipo=Monitor&q=HP">HP</a></li>
            <li><a href="?tipo=Monitor&q=LG">LG</a></li>
            <li><a href="?tipo=Monitor&q=MSI">MSI</a></li>
          </ul>
        </li>
      </ul>
  <div class="caixa-container">
  <div class="botao-link"  onclick="window.location.href='./loja.php';">
        Voltar atrás
  </div>
    </aside>

    <main class="content" id="content">
      <h3 class="nao-encontrado">A Carregar produtos...</h3>
    </main>
  </div>
</div>

<script>
  document.querySelectorAll('.sidebar a').forEach(link => {
    link.addEventListener('click', function (e) {
      const parentLi = this.closest('.has-sub');

      if (parentLi && this === parentLi.querySelector(':scope > a') && this.nextElementSibling) {
        e.preventDefault();
        parentLi.classList.toggle('open');
        return;
      }

      const href = this.getAttribute('href');

      if (href.includes('perifericos_lista.php')) {
        e.preventDefault();
        fetch(href)
          .then(response => {
            if (!response.ok) throw new Error('Erro ao carregar os dados');
            return response.text();
          })
          .then(html => {
            document.getElementById('content').innerHTML = html;
          })
          .catch(err => {
            console.error(err);
            alert('Não foi possível carregar os periféricos.');
          });
        return;
      }

      if (href.startsWith('?tipo=')) {
        e.preventDefault();
        const url = 'perifericos_lista.php' + href;

        fetch(url)
          .then(response => {
            if (!response.ok) throw new Error('Erro ao carregar os dados');
            return response.text();
          })
          .then(html => {
            document.getElementById('content').innerHTML = html;
          })
          .catch(err => {
            console.error(err);
            alert('Não foi possível carregar os periféricos.');
          });
      }
    });
  });

  window.addEventListener('DOMContentLoaded', () => {
    fetch('perifericos_lista.php')
      .then(response => {
        if (!response.ok) throw new Error('Erro ao carregar os dados');
        return response.text();
      })
      .then(html => {
        document.getElementById('content').innerHTML = html;
      })
      .catch(err => {
        console.error(err);
        document.getElementById('content').innerHTML = '<p>Não foi possível carregar os produtos.</p>';
      });
  });
</script>

</body>
</html>
