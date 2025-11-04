<?php
$host = 'localhost';
$db = 'mysql';
$user = 'root';
$pass = '';

// Conexão PDO com charset explícito para evitar problemas de codificação (mojibake)
try {
    $dsn = "mysql:host=$host;port=3306;dbname=$db;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // Garante que a conexão use UTF-8 desde o início
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
}
?>
