<?php
session_start();
include_once __DIR__ . '/../db.php';
include_once __DIR__ . '/../cabecindex.php';
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../index/index.php");
    exit;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../PHPMailer/PHPMailer.php';
require_once '../PHPMailer/SMTP.php';
require_once '../PHPMailer/Exception.php';

$compraId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$compraId) {
    echo '<p>ID da compra inválido.</p>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['compra_id'], $_POST['novo_estado'])) {
    $novoEstado   = $_POST['novo_estado'];
    $estadosValid = ['Pendente', 'A caminho', 'Entregue'];

    if (in_array($novoEstado, $estadosValid, true)) {
        // Atualiza estado
        $stmtUpd = $conn->prepare('UPDATE fim_compra SET estado = ? WHERE id = ?');
        $stmtUpd->bind_param('si', $novoEstado, $compraId);
        $stmtUpd->execute();
        $stmtUpd->close();

        // Buscar info do utilizador
        $stmtUser = $conn->prepare("SELECT u.nome, u.email, fc.data_compra 
                                    FROM fim_compra fc 
                                    JOIN utilizadores u ON fc.utilizador_id = u.id 
                                    WHERE fc.id = ?");
        $stmtUser->bind_param("i", $compraId);
        $stmtUser->execute();
        $resUser = $stmtUser->get_result();

        if ($resUser->num_rows > 0) {
            $dados = $resUser->fetch_assoc();
            $userNome = $dados['nome'];
            $userEmail = $dados['email'];
            $dataCompra = date('d/m/Y H:i', strtotime($dados['data_compra']));
        }
        $stmtUser->close();

        // Buscar produtos
        $stmtItens = $conn->prepare("SELECT nome_produto, quantidade, preco FROM fim_compra_itens WHERE compra_id = ?");
        $stmtItens->bind_param("i", $compraId);
        $stmtItens->execute();
        $resItens = $stmtItens->get_result();

        $nomesProdutos = [];
        $total = 0;
        while ($item = $resItens->fetch_assoc()) {
            $nomesProdutos[] = $item['nome_produto'] . ' (x' . $item['quantidade'] . ')';
            $total += $item['quantidade'] * $item['preco'];
        }
        $stmtItens->close();

        // Envia email
        $mail = new PHPMailer(true);
        try {
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'pcmastergeral@gmail.com';
            $mail->Password   = 'mjsv oxar shbz dfzp';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('pcmastergeral@gmail.com', 'PcMaster');
            $mail->addAddress($userEmail, $userNome);
            $mail->isHTML(true);
            $mail->Subject = "Encomenda PcMaster";

            $produtosStr = implode(', ', $nomesProdutos);
            $totalFormatado = number_format($total, 2, ',', '.');

            $mail->Body = "
                <p>Olá <strong>{$userNome}</strong>,</p>
                <p>O estado da sua encomenda foi atualizado.
                <p><strong>Produto(s):</strong> {$produtosStr}</p>
                <p><strong>Total:</strong> {$totalFormatado} €</p> 
                <p><strong>Estado: </strong>{$novoEstado}.</p>
                <p><strong>Data da compra:</strong> {$dataCompra}</p>
                <br>
                <p>Obrigado por comprar na <strong>PcMaster</strong>!</p>
            ";

            $mail->send();
        } catch (Exception $e) {
            error_log("Erro ao enviar email: " . $mail->ErrorInfo);
        }
    }

    $redirect = 'detalhes_compra.php?id=' . $compraId;
    if (isset($_GET['user_id'])) {
        $redirect .= '&user_id=' . (int)$_GET['user_id'];
    }
    header('Location: ' . $redirect);
    exit;
}

$stmtCompra = $conn->prepare("SELECT fc.*, u.nome AS user_nome, u.email AS user_email
                              FROM fim_compra fc
                              JOIN utilizadores u ON u.id = fc.utilizador_id
                              WHERE fc.id = ?");
$stmtCompra->bind_param('i', $compraId);
$stmtCompra->execute();
$resultCompra = $stmtCompra->get_result();

if ($resultCompra->num_rows === 0) {
    echo '<p>Compra não encontrada.</p>';
    exit;
}
$compra = $resultCompra->fetch_assoc();
$stmtCompra->close();

$stmtItens = $conn->prepare("SELECT nome_produto, tipo_produto, quantidade, preco
                             FROM fim_compra_itens
                             WHERE compra_id = ?");
$stmtItens->bind_param('i', $compraId);
$stmtItens->execute();
$resultItens = $stmtItens->get_result();

$total = 0;
$itens = [];
while ($row = $resultItens->fetch_assoc()) {
    $row['subtotal'] = $row['quantidade'] * $row['preco'];
    $total += $row['subtotal'];
    $itens[] = $row;
}
$stmtItens->close();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Detalhes da Compra #<?php echo $compraId; ?></title>
    <link rel="stylesheet" href="../css/admin_produto.css">
    <link rel="icon" type="image/png" href="../imagens/icon.png">
</head>
<body>
<div class="bg">
    <div class="overlay"></div>
        <br><br><br>
    <div class="content" style="max-width:900px;">
        <table class="admin-table">
            <thead>
                <tr><th colspan="4"><h2>Detalhes da Compra #<?php echo $compraId; ?></h2></th></tr>
            </thead>
            <tbody>
                <tr>
                    <th>Utilizador</th>
                    <td><?php echo htmlspecialchars($compra['user_nome']); ?></td>
                    <th>Email</th>
                    <td><?php echo htmlspecialchars($compra['user_email']); ?></td>
                </tr>
                <tr>
                    <th>Data</th>
                    <td><?php echo date('d/m/Y H:i', strtotime($compra['data_compra'])); ?></td>
                    <th>Estado</th>
                    <td>
                        <form method="POST" class="estado-form" style="display:inline;">
                            <input type="hidden" name="compra_id" value="<?php echo $compraId; ?>">
                            <select name="novo_estado" onchange="this.form.submit()">
                                <?php
                                foreach (['Pendente', 'A caminho', 'Entregue'] as $opt) {
                                    $sel = ($compra['estado'] === $opt) ? 'selected' : '';
                                    echo '<option value="' . $opt . '" ' . $sel . '>' . $opt . '</option>';
                                }
                                ?>
                            </select>
                        </form>
                    </td>
                </tr>
                <tr>
                    <th>Endereço</th>
                    <td colspan="3">
                        <?php echo htmlspecialchars($compra['rua'] . ', ' . $compra['distrito'] . ' (' . $compra['codigo_postal'] . ')'); ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <br>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Tipo</th>
                    <th>Quantidade</th>
                    <th>Preço (€)</th>
                    <th>Subtotal (€)</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($itens) {
                    foreach ($itens as $item) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($item['nome_produto']) . '</td>';
                        echo '<td>' . htmlspecialchars(ucfirst($item['tipo_produto'])) . '</td>';
                        echo '<td>' . $item['quantidade'] . '</td>';
                        echo '<td>' . number_format($item['preco'], 2, ',', '.') . '</td>';
                        echo '<td>' . number_format($item['subtotal'], 2, ',', '.') . '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="5">Nenhum item encontrado.</td></tr>';
                } ?>
                <tr>
                    <th colspan="4" style="text-align:right;">Total:</th>
                    <td><strong><?php echo number_format($total, 2, ',', '.'); ?></strong></td>
                </tr>
            </tbody>
        </table>
        <br>
        <a href="admin_compras.php<?php echo ($userId = filter_input(INPUT_GET, 'user_id', FILTER_SANITIZE_NUMBER_INT)) ? '?user_id=' . $userId : ''; ?>" class="btn voltar">Voltar</a>
    </div>
</div>
</body>
</html>
