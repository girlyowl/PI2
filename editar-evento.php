<?php
include('db.php');

if (!isset($_GET['id'])) {
    header('Location: gerenciar-eventos.php');
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM eventos WHERE id = ?");
$stmt->execute([$id]);
$evento = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $data = $_POST['data'];
    $horario = $_POST['horario'];
    $cidade = $_POST['cidade'];
    $endereco = $_POST['endereco'];

    $stmt = $pdo->prepare("UPDATE eventos SET titulo = ?, descricao = ?, data = ?, horario = ?, cidade = ?, endereco = ? WHERE id = ?");
    $stmt->execute([$titulo, $descricao, $data, $horario, $cidade, $endereco, $id]);

    header('Location: gerenciar-eventos.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Evento - Eventos Baixada Santista</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100">

<header class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white p-6">
    <div class="max-w-6xl mx-auto flex justify-between items-center">
        <h1 class="text-3xl font-extrabold">Eventos Baixada Santista</h1>
        <div class="flex space-x-4">
            <a href="index.php" class="bg-white text-indigo-600 px-4 py-2 rounded-lg shadow hover:bg-indigo-100 transition">Voltar</a>
        </div>
    </div>
</header>

<div class="max-w-3xl mx-auto mt-10 bg-white p-8 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Editar Evento</h2>

    <form method="post" class="space-y-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Título:</label>
            <input type="text" name="titulo" value="<?php echo h($evento['titulo']); ?>" required class="w-full border border-gray-300 rounded px-4 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Descrição:</label>
            <textarea name="descricao" required class="w-full border border-gray-300 rounded px-4 py-2 h-32"><?php echo h($evento['descricao']); ?></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data:</label>
                <input type="date" name="data" value="<?php echo h($evento['data']); ?>" required class="w-full border border-gray-300 rounded px-4 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Horário:</label>
                <input type="time" name="horario" value="<?php echo h($evento['horario']); ?>" required class="w-full border border-gray-300 rounded px-4 py-2">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Cidade:</label>
            <input type="text" name="cidade" value="<?php echo h($evento['cidade']); ?>" required class="w-full border border-gray-300 rounded px-4 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Endereço:</label>
            <input type="text" name="endereco" value="<?php echo h($evento['endereco'] ?? ''); ?>" class="w-full border border-gray-300 rounded px-4 py-2">
        </div>

        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg shadow hover:bg-indigo-700 transition">
            Salvar Alterações
        </button>
    </form>
</div>

</body>
</html>
