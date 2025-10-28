<?php
$host = 'localhost';
$db = 'mysql';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;port=3306;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
}
?>
