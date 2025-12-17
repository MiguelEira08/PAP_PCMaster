<?php
session_start();
require_once __DIR__ . '/../db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$erro = '';
$MAX_TENTATIVAS = 5;
$BLOQUEIO_MINUTOS = 15;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome']);
    $password = trim($_POST['password']);
    $ip = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $agora = date('Y-m-d H:i:s');

    if (empty($nome) || empty($password)) {
        $erro = 'Preencha todos os campos!';
        goto fim;
    }

    /* ===============================
       VERIFICAR BLOQUEIO
    =============================== */
    $stmt = $conn->prepare("
        SELECT tentativas, bloqueado_ate
        FROM login_tentativas
        WHERE nome = ? AND ip = ?
    ");
    $stmt->bind_param("ss", $nome, $ip);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($row = $resultado->fetch_assoc()) {
        if ($row['bloqueado_ate'] && $row['bloqueado_ate'] > $agora) {
            $erro = 'Conta temporariamente bloqueada. Tente mais tarde.';
            goto fim;
        }
    }

    /* ===============================
       BUSCAR UTILIZADOR
    =============================== */
    $stmt = $conn->prepare("
        SELECT id, nome, password, tipo
        FROM utilizadores
        WHERE nome = ?
    ");
    $stmt->bind_param("s", $nome);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $erro = 'Utilizador não encontrado!';
        goto registar_falha;
    }

    $stmt->bind_result($id, $nome_bd, $senha_bd, $tipo);
    $stmt->fetch();

    /* ===============================
       VERIFICAR PASSWORD
    =============================== */
    if (!password_verify($password, $senha_bd)) {
        $erro = 'Senha incorreta!';
        goto registar_falha;
    }

    /* ===============================
       LOGIN COM SUCESSO
    =============================== */
    session_regenerate_id(true);

    $_SESSION['user_id'] = $id;
    $_SESSION['nome'] = $nome_bd;
    $_SESSION['tipo'] = $tipo;
    $_SESSION['LAST_ACTIVITY'] = time();

    // Limpar tentativas anteriores
    $stmt = $conn->prepare("
        DELETE FROM login_tentativas
        WHERE nome = ? AND ip = ?
    ");
    $stmt->bind_param("ss", $nome, $ip);
    $stmt->execute();

    // Redirecionar
    if ($tipo === 'admin') {
        header("Location: ../admin/admin_dashboard.php");
    } else {
        header("Location: ../index/index.php");
    }
    exit();


    /* ===============================
       REGISTAR FALHA
    =============================== */
    registar_falha:

    $stmt = $conn->prepare("
        INSERT INTO login_tentativas (nome, ip, tentativas, ultimo_login, user_agent)
        VALUES (?, ?, 1, NOW(), ?)
        ON DUPLICATE KEY UPDATE
            tentativas = tentativas + 1,
            ultimo_login = NOW(),
            user_agent = ?
    ");
    $stmt->bind_param("ssss", $nome, $ip, $userAgent, $userAgent);
    $stmt->execute();

    // Bloquear se atingir limite
    $stmt = $conn->prepare("
        UPDATE login_tentativas
        SET bloqueado_ate = DATE_ADD(NOW(), INTERVAL ? MINUTE)
        WHERE nome = ? AND ip = ? AND tentativas >= ?
    ");
    $stmt->bind_param("isii", $BLOQUEIO_MINUTOS, $nome, $ip, $MAX_TENTATIVAS);
    $stmt->execute();

    fim:
    $conn->close();
    $_SESSION['erro_login'] = $erro;
    header("Location: login.php");
    exit();
}
