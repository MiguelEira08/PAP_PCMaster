<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'pap_gustavo_miguel';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die('Erro: ' . $conn->connect_error); }
define('BASE_URL', '/PcMaster/PAP');
