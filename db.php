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

// Helper para garantir saída em UTF-8 e escapar HTML com segurança
if (!function_exists('h')) {
    function h($value) {
        if ($value === null) return '';
        // Se já for UTF-8 válido, apenas escape; caso contrário, converte de ISO-8859-1 para UTF-8
        if (!mb_check_encoding($value, 'UTF-8')) {
            $value = mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
        }
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

?>
?>
