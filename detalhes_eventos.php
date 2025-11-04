<?php
// Inclui o arquivo de conexão com o banco de dados
session_start();
include('db.php'); 

$evento = null;
$mensagem_erro = '';

// 1. Pega o ID do evento da URL
$id_evento = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id_evento) {
    $mensagem_erro = "Erro: ID do evento não fornecido ou inválido.";
} else {
    try {
        // --- 2. BUSCA PRINCIPAL: Obter os detalhes do evento ---
        // Adicione todos os campos que você deseja exibir na sua consulta SELECT
        $sql_evento = "SELECT id, nome, data_hora, local, descricao FROM eventos WHERE id = :id";
        $stmt_evento = $pdo->prepare($sql_evento);
        $stmt_evento->bindParam(':id', $id_evento, PDO::PARAM_INT);
        $stmt_evento->execute();
        $evento = $stmt_evento->fetch(); // Tenta buscar o evento

        if (!$evento) {
            $mensagem_erro = "Erro: Evento não encontrado.";
        }

    } catch (PDOException $e) {
        $mensagem_erro = "Erro no banco de dados: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?php echo $evento ? h($evento['nome']) : 'Detalhes do Evento'; ?></title>
    <link rel="stylesheet" href="styles.css"> 
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f9; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        p { line-height: 1.6; color: #555; }
        .info-box { background-color: #e9ecef; padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        /* Removemos o estilo dos participantes */
    </style>
</head>
<body>

<div class="container">
    <a href="listar_eventos.php" style="text-decoration: none;">&larr; Voltar para a Lista de Eventos</a>
    
    <?php if ($mensagem_erro): ?>
        <h2 style="color: red;"><?php echo $mensagem_erro; ?></h2>
    <?php elseif ($evento): ?>
        
    <h1><?php echo h($evento['nome']); ?></h1>

        <div class="info-box">
            <p><strong>🗓️ Data e Hora:</strong> <?php echo date('d/m/Y H:i', strtotime($evento['data_hora'])); ?></p>
            <p><strong>📍 Local:</strong> <?php echo h($evento['local']); ?></p>
        </div>

        <h2>Detalhes do Evento</h2>
    <p><?php echo nl2br(h($evento['descricao'] ?? 'Nenhuma descrição detalhada fornecida.')); ?></p>

        <hr>
        
        <a href="editar-evento.php?id=<?php echo $evento['id']; ?>">✏️ Editar este Evento</a>

    <?php endif; ?>

</div>

</body>
</html>