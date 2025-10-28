<?php
include('db.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM eventos WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: gerenciar-eventos.php');
exit;
