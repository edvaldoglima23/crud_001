<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "desafio";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

//utf-8
$conn->set_charset("utf8mb4");

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>
