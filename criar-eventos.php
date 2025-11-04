<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    echo "Acesso negado. Faça login para acessar esta página.";
    exit;
}

include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criar_evento'])) {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $data = $_POST['data'];
    $horario = $_POST['horario'];
    $endereco = $_POST['endereco'];
    $cidade = $_POST['cidade'];
    $imagem = '';

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $imagem_nome = $_FILES['imagem']['name'];
        $imagem_tmp = $_FILES['imagem']['tmp_name'];
        $imagem_destino = 'uploads/' . basename($imagem_nome);

        if (move_uploaded_file($imagem_tmp, $imagem_destino)) {
            $imagem = $imagem_destino;
        } else {
            echo "Erro ao fazer upload da imagem.";
        }
    }

    try {
        $sql = "INSERT INTO eventos (titulo, descricao, data, horario, endereco, cidade, imagem, usuario_id) 
                VALUES (:titulo, :descricao, :data, :horario, :endereco, :cidade, :imagem, :usuario_id)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([ 
            ':titulo' => $titulo,
            ':descricao' => $descricao,
            ':data' => $data,
            ':horario' => $horario,
            ':endereco' => $endereco,
            ':cidade' => $cidade,
            ':imagem' => $imagem,
            ':usuario_id' => $_SESSION['usuario_id']
        ]);
    
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        echo "Erro ao criar evento: " . $e->getMessage();
    }
}



$sql_cidades = "SELECT cidade FROM cidades_baixada ORDER BY cidade";
$stmt_cidades = $pdo->prepare($sql_cidades);
$stmt_cidades->execute();
$cidades = $stmt_cidades->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Criar Evento - Baixada Santista</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-gray-50 criar-eventos-page">

<header class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white p-6">
  <div class="max-w-6xl mx-auto flex justify-between items-center">
    <h1 class="text-3xl font-extrabold">Criar Evento - Baixada Santista</h1>
    <div class="flex items-center gap-4">
      <a href="index.php" class="bg-white text-indigo-600 px-4 py-2 rounded-lg shadow-md hover:bg-indigo-100 transition-all">Voltar</a>
    </div>
  </div>
</header>

<div class="max-w-6xl mx-auto p-6">
    <form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="titulo" class="block text-sm font-medium text-gray-700">Título:</label>
                <input type="text" name="titulo" id="titulo" class="border border-gray-300 rounded-lg px-4 py-2 w-full" required>
            </div>
            <div>
                <label for="descricao" class="block text-sm font-medium text-gray-700">Descrição:</label>
                <textarea name="descricao" id="descricao" class="border border-gray-300 rounded-lg px-4 py-2 w-full" rows="4" required></textarea>
            </div>
            <div>
                <label for="data" class="block text-sm font-medium text-gray-700">Data:</label>
                <input type="date" name="data" id="data" class="border border-gray-300 rounded-lg px-4 py-2 w-full" required>
            </div>
            <div>
                <label for="horario" class="block text-sm font-medium text-gray-700">Horário:</label>
                <input type="time" name="horario" id="horario" class="border border-gray-300 rounded-lg px-4 py-2 w-full" required>
            </div>
            <div>
                <label for="endereco" class="block text-sm font-medium text-gray-700">Endereço:</label>
                <input type="text" name="endereco" id="endereco" class="border border-gray-300 rounded-lg px-4 py-2 w-full">
            </div>
            <div>
                <label for="cidade" class="block text-sm font-medium text-gray-700">Cidade:</label>
                <select name="cidade" id="cidade" class="border border-gray-300 rounded-lg px-4 py-2 w-full" required>
                    <option value="">Selecione uma cidade</option>
                    <?php foreach ($cidades as $cidade_opcao): ?>
                        <option value="<?= htmlspecialchars($cidade_opcao['cidade']) ?>">
                            <?= htmlspecialchars($cidade_opcao['cidade']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="sm:col-span-2">
                <label for="imagem" class="block text-sm font-medium text-gray-700">Imagem do Evento:</label>
                <input type="file" name="imagem" id="imagem" class="border border-gray-300 rounded-lg px-4 py-2 w-full">
            </div>
        </div>
        <div class="mt-6 text-center">
            <button type="submit" name="criar_evento" class="bg-indigo-600 text-white px-6 py-2 rounded-lg shadow-md hover:bg-indigo-700 transition-all">Criar Evento</button>
        </div>
    </form>
</div>

</body>
</html>
