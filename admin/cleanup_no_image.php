<?php
session_start();
include(__DIR__ . '/../db.php');

// Segurança: somente usuários autenticados e com permissão (admin ou owner id=1)
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    http_response_code(403);
    echo "Acesso negado. Faça login como administrador para usar esta página.";
    exit;
}

$usuario_id = $_SESSION['usuario_id'] ?? null;
$usuario_tipo = $_SESSION['usuario_tipo'] ?? null;
$is_admin = ($usuario_tipo === 'admin' || $usuario_id == 1);

if (!$is_admin) {
    http_response_code(403);
    echo "Acesso negado. Sua conta não tem permissão para executar essa operação.";
    exit;
}

// CSRF token
if (empty($_SESSION['cleanup_csrf'])) {
    $_SESSION['cleanup_csrf'] = bin2hex(random_bytes(16));
}
$token = $_SESSION['cleanup_csrf'];

function esc($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

// Helper SQL conditions
$where = "(imagem IS NULL OR imagem = '')";

// Actions
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $posted_token = $_POST['csrf'] ?? '';
    if (!hash_equals($token, $posted_token)) {
        $message = 'Token CSRF inválido.';
    } else {
        try {
            if ($action === 'quarantine') {
                // create quarantine table if not exists
                $pdo->exec("CREATE TABLE IF NOT EXISTS eventos_quarentena LIKE eventos");
                // copy rows
                $stmt = $pdo->prepare("INSERT INTO eventos_quarentena SELECT * FROM eventos WHERE $where");
                $stmt->execute();
                $count = $stmt->rowCount();
                $message = "Quarentena criada: $count registro(s) copiados para eventos_quarentena.";
            } elseif ($action === 'delete') {
                // transactional copy + delete
                $pdo->beginTransaction();
                $pdo->exec("CREATE TABLE IF NOT EXISTS eventos_quarentena LIKE eventos");
                $insert = $pdo->prepare("INSERT INTO eventos_quarentena SELECT * FROM eventos WHERE $where");
                $insert->execute();
                $del = $pdo->prepare("DELETE FROM eventos WHERE $where");
                $del->execute();
                $deleted = $del->rowCount();
                $pdo->commit();
                $message = "Operação concluída: $deleted registro(s) removidos e copiados para eventos_quarentena.";
            } else {
                $message = 'Ação desconhecida.';
            }
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $message = 'Erro durante a operação: ' . esc($e->getMessage());
        }
    }
}

// Fetch list for preview
$listStmt = $pdo->prepare("SELECT id, titulo, cidade, data, horario, usuario_id, imagem FROM eventos WHERE $where ORDER BY id DESC LIMIT 1000");
$listStmt->execute();
$events = $listStmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Admin: Limpar eventos sem imagem</title>
    <link href="/PI2/styles.css" rel="stylesheet">
    <style> body{font-family:system-ui,Segoe UI,Arial,sans-serif;padding:20px;} table{width:100%;border-collapse:collapse} th,td{padding:8px;border:1px solid #ddd;text-align:left} .danger{background:#ffe6e6;border:1px solid #ffb3b3;padding:10px}</style>
</head>
<body>
    <h1>Admin — Eventos sem imagem</h1>
    <?php if ($message): ?>
        <div class="danger"><?php echo esc($message); ?></div>
    <?php endif; ?>

    <p>Listando eventos onde a coluna <code>imagem</code> é NULL ou vazia. Total: <strong><?php echo count($events); ?></strong></p>

    <?php if (count($events) === 0): ?>
        <p>Nenhum registro encontrado. Nada a fazer.</p>
    <?php else: ?>
        <form method="post" onsubmit="return confirm('Tem certeza? Esta ação é irreversível após a exclusão. Recomendado: primeiro clique em Quarentenar');">
            <input type="hidden" name="csrf" value="<?php echo esc($token); ?>">
            <button type="submit" name="action" value="quarantine">Quarentenar (copiar para eventos_quarentena)</button>
            <button type="submit" name="action" value="delete" style="margin-left:12px;color:#fff;background:#c0392b;padding:8px 12px;border:none;border-radius:4px;">Excluir permanentemente (copiar antes)</button>
        </form>

        <table style="margin-top:16px">
            <thead><tr><th>ID</th><th>Título</th><th>Cidade</th><th>Data</th><th>Horário</th><th>Usuario</th><th>Imagem</th></tr></thead>
            <tbody>
                <?php foreach ($events as $ev): ?>
                    <tr>
                        <td><?php echo esc($ev['id']); ?></td>
                        <td><?php echo esc($ev['titulo']); ?></td>
                        <td><?php echo esc($ev['cidade']); ?></td>
                        <td><?php echo esc($ev['data']); ?></td>
                        <td><?php echo esc($ev['horario']); ?></td>
                        <td><?php echo esc($ev['usuario_id']); ?></td>
                        <td><?php echo esc($ev['imagem']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</body>
</html>
