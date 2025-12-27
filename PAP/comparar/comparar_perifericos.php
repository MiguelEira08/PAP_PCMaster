<?php
session_start();
include_once __DIR__ . '/../db.php';
include_once __DIR__ . '/../cabecindex.php';
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Loja Componentes</title>
    <link rel="stylesheet" href="../css/comparar.css">
    <link rel="icon" type="image/png" href="../imagens/icon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="bg">
    <div class="overlay"></div>  
    <div class="content">
    <div class="sidebar">
               <ul>
        <li><a href="comparar_perifericos_lista.php">Todos os Tipos</a></li>
        <li class="has-sub"><a href="#">Fones</a>
          <ul class="sub-menu">
            <li><a href="comparar_perifericos_lista.php?tipo=Fones">Todos</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Fones&q=Asus">Asus</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Fones&q=Corsair">Corsair</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Fones&q=HyperX">HyperX</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Fones&q=Logitech">Logitech</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Fones&q=Mars Gaming">Mars Gaming</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Fones&q=Razer">Razer</a></li>

          </ul>
        </li>
        <li class="has-sub"><a href="#">Teclados</a>
          <ul class="sub-menu">
            <li><a href="comparar_perifericos_lista.php?tipo=Teclado">Todos</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Teclado&q=Asus">Asus</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Teclado&q=Corsair">Corsair</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Teclado&q=Gigabyte">Gigabyte</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Teclado&q=HyperX">HyperX</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Teclado&q=Logitech">Logitech</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Teclado&q=Mars Gaming">Mars Gaming</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Teclado&q=Razer">Razer</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Teclado&q=SteelSeries">SteelSeries</a></li>
          </ul>
        </li>
        <li class="has-sub"><a href="#">Ratos</a>
          <ul class="sub-menu">
            <li><a href="comparar_perifericos_lista.php?tipo=Rato">Todos</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Rato&q=Asus">Asus</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Rato&q=Corsair">Corsair</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Rato&q=Gigabyte">Gigabyte</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Rato&q=HyperX">HyperX</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Rato&q=Logitech">Logitech</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Rato&q=Mars Gaming">Mars Gaming</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Rato&q=NPlay">NPlay</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Rato&q=Razer">Razer</a></li>
            
          </ul>
        </li>
        <li class="has-sub"><a href="#">Tapetes de Rato</a>
          <ul class="sub-menu">
            <li><a href="comparar_perifericos_lista.php?tipo=Tapete">Todos</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Tapete&q=Asus">Asus</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Tapete&q=Corsair">Corsair</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Tapete&q=Logitech">Logitech</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Tapete&q=Mars Gaming">Mars Gaming</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Tapete&q=Razer">Razer</a></li>
          </ul>
        </li>
        <li class="has-sub"><a href="#">Monitores</a>
          <ul class="sub-menu">
            <li><a href="comparar_perifericos_lista.php?tipo=Monitor">Todos</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Monitor&q=Asus">Asus</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Monitor&q=AOC">AOC</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Monitor&q=Acer">Acer</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Monitor&q=BenQ">BenQ</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Monitor&q=Gigabyte">Gigabyte</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Monitor&q=HP">HP</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Monitor&q=LG">LG</a></li>
            <li><a href="comparar_perifericos_lista.php?tipo=Monitor&q=MSI">MSI</a></li>
          </ul>
        </li>
      </ul>
        <div class="caixa-container">
            <div class="botao-link"  onclick="window.location.href='./comparar.php';">
                Voltar atrás
        </div>
</div>
</div>


<div class="content">
        <div class="comparacao-box" id="caixa-comparacao">
            <h2>Comparação de Produtos</h2>
            <div id="produto1">Produto 1: Nenhum</div>
            <div id="produto2">Produto 2: Nenhum</div>
            <div class="alinhar"><button class="comparar-btn" onclick="comparar()">Comparar</button></div>
            <div id="resultado-comparacao"></div>
        </div>
        
<div id="lista-produtos">
        <div class="produtos-linha">
        <?php
        $tipoSelecionado = isset($_GET['tipo']) ? strtolower($_GET['tipo']) : null;
        $sql = "SELECT * FROM perifericos";
        if ($tipoSelecionado) {
            $sql .= " WHERE tipo = '$tipoSelecionado'";
        }
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $produtoJS = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                echo '<div class="produto-card">';
                    echo '<img class="produto-imagem" src="../imagens/' . $row["caminho_arquivo"] . '" alt="' . htmlspecialchars($row["nome"]) . '">';
                    echo '<h2 class="produto-nome">' . htmlspecialchars($row["nome"]) . '</h2>';
                    echo '<a class="botao-visualizar" href="javascript:void(0)" onclick="adicionarComparacao(' . $produtoJS . ')">Adicionar à comparação</a>';
                echo '</div>';
            }
        } else {
            echo "<p>Nenhum periférico encontrado.</p>";  
        }
        ?>
        </div>
    </div>
    </div>
</div>

<script>
let produto1 = null;
let produto2 = null;

function atualizarCaixa() {
  document.getElementById("produto1").innerText = produto1 ? "Produto 1: " + produto1.nome : "Produto 1: Nenhum";
  document.getElementById("produto2").innerText = produto2 ? "Produto 2: " + produto2.nome : "Produto 2: Nenhum";
}

function adicionarComparacao(produto) {
  if (!produto1) produto1 = produto;
  else if (!produto2 && produto.id !== produto1.id) produto2 = produto;
  else if (produto.id === produto1.id || produto.id === produto2?.id) {
    alert("Este produto já foi selecionado.");
    return;
  } else {
    alert("Só pode comparar dois produtos de cada vez.");
    return;
  }
  atualizarCaixa();
}

function comparar() {
  if (!produto1 || !produto2) {
    alert("Selecione dois produtos para comparar.");
    return;
  }

  const div = document.getElementById("resultado-comparacao");
  div.innerHTML = `
    <div class="descricao-produto"><strong>${produto1.nome}</strong><br>${produto1.descricao}</div>
    <div class="descricao-produto"><strong>${produto2.nome}</strong><br>${produto2.descricao}</div>
  `;
}


document.querySelectorAll('.sidebar a').forEach(link => {
  link.addEventListener('click', function (e) {
    const parentLi = this.closest('.has-sub');

    if (parentLi && this === parentLi.querySelector(':scope > a') && this.nextElementSibling) {
      e.preventDefault();
      parentLi.classList.toggle('open');
      return;
    }

    if (this.getAttribute('href') && this.getAttribute('href').includes('comparar_perifericos_lista.php')) {
      e.preventDefault();
      fetch(this.getAttribute('href'))
        .then(response => response.text())
        .then(html => {
          document.getElementById('lista-produtos').innerHTML = html;
        })
        .catch(err => {
          console.error(err);
          alert('Não foi possível carregar os periféricos.');
        });
    }
  });
});

window.addEventListener('DOMContentLoaded', () => {
  fetch('comparar_perifericos_lista.php')
    .then(response => response.text())
    .then(html => {
      document.getElementById('lista-produtos').innerHTML = html;
    });
});

</script>

</body>
</html>
