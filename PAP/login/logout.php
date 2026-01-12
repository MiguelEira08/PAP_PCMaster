<?php
session_start();
require_once '../db.php'; 

if (isset($_SESSION['user_id'])) {
    $id = $_SESSION['user_id'];

    $stmt = $conn->prepare("
        UPDATE utilizador_seguranca 
        SET ultimo_logout = NOW() 
        WHERE utilizador_id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

session_unset();
session_destroy();

header("Location: ../index/index.php?logout=1");
exit();
