<?php
session_start();
    include_once '../db.php';
      $tipoSelecionado = isset($_GET['tipo']) ? $_GET['tipo'] : null;
      $qSelecionado = isset($_GET['q']) ? $_GET['q'] : null;

      $sql = "SELECT * FROM perifericos";
      $conditions = [];
      $params = [];
      $types = "";

      if ($tipoSelecionado) {
        $conditions[] = "tipo = ?";
        $params[] = $tipoSelecionado;
        $types .= "s";
      }

      if ($qSelecionado) {
        $conditions[] = "(marca LIKE ? OR nome LIKE ?)";
        $qLike = "%" . $qSelecionado . "%";
        $params[] = $qLike;
        $params[] = $qLike;
        $types .= "ss";
      }

      if (count($conditions) > 0) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
      }

      if (count($params) > 0) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
      } else {
        $result = mysqli_query($conn, "SELECT * FROM perifericos");
      }

      if ($result && mysqli_num_rows($result) > 0) {
        echo '<div class="grid-produtos" style="display: flex; flex-wrap: wrap; gap: 24px; justify-content: flex-start;">';
        while($row = mysqli_fetch_assoc($result)) {
          echo '<div class="cartao-produto" style="width:300px; height:400px; display:flex; flex-direction:column; justify-content:space-between; box-sizing:border-box;">';
            echo '<div class="cartao-imagem" style="width:100%; height:250px; display:flex; align-items:center; justify-content:center; overflow:hidden;">';
              echo '<img src="../imagens/'. $row["caminho_arquivo"] .'" alt="Imagem de '. htmlspecialchars($row["nome"]) .'" style="max-width:100%; max-height:100%; object-fit:contain;">';
            echo '</div>';
            echo '<div class="cartao-detalhes" style="flex:1; display:flex; flex-direction:column; justify-content:space-between; align-items:center;">';
              echo '<div class="manrope-titulo" text-align:center;">'. htmlspecialchars($row["nome"]) .'</div>';
              echo '<h4 class="preco" style="margin:5px 0 10px 0;">€'. number_format($row["preco"], 2) .'</h4>';
echo '<a href="produto_periferico.php?id='.$row["id"].'" class="btn-adicionar">Visualizar</a>';            
echo '</div>';
echo '</div>';
        }
        echo '</div>';
      } else {
        echo '<p class="nao-encontrado">Nenhum periférico encontrado.</p>';
      }
      ?>