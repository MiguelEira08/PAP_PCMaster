<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$destino = $_SERVER['HTTP_REFERER'] ?? '/PcMaster/PAP/index/index.php';
?>

<a href="<?php echo htmlspecialchars($destino); ?>" class="botao-voltar voltar-fixo">
    ← Voltar
</a>
