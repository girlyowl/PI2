<?php
include('db.php');

$query = isset($_GET['query']) ? $_GET['query'] : '';
$cidade = isset($_GET['cidade']) ? $_GET['cidade'] : '';
$data = isset($_GET['data']) ? $_GET['data'] : '';

$sql = "SELECT * FROM eventos WHERE 1=1";
$params = [];

if (!empty($query)) {
    $sql .= " AND (titulo LIKE :query OR descricao LIKE :query)";
    $params[':query'] = "%$query%";
}

if (!empty($cidade)) {
    $sql .= " AND cidade LIKE :cidade";
    $params[':cidade'] = "%$cidade%";
}

if (!empty($data)) {
    $sql .= " AND data = :data";
    $params[':data'] = $data;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$eventos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Resultados da Pesquisa - Eventos Baixada Santista</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-gray-100">

<header class="bg-blue-600 text-white p-4">
  <div class="max-w-6xl mx-auto flex justify-between items-center">
    <h1 class="text-xl font-bold">Eventos Baixada Santista</h1>
    <div class="space-x-4">
      <a href="index.php" class="bg-white text-blue-600 px-4 py-2 rounded hover:bg-blue-100">Voltar para a Página Inicial</a>
    </div>
  </div>
</header>

<div class="max-w-7xl mx-auto p-4">
    <?php if ($eventos): ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($eventos as $evento): ?>
                <div class="bg-white shadow-md rounded-md overflow-hidden">
                    <?php if (!empty($evento['imagem']) && file_exists($evento['imagem'])): ?>
                        <img src="<?php echo $evento['imagem']; ?>" alt="Imagem do evento" class="w-full h-56 object-cover">
                    <?php endif; ?>
                    <div class="p-4">
                        <h2 class="text-xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($evento['titulo']); ?></h2>
                        <p class="text-gray-600 mb-2"><?php echo nl2br(htmlspecialchars($evento['descricao'])); ?></p>
                        <p class="text-sm text-gray-500"><strong>Data:</strong> <?php echo date('d/m/Y', strtotime($evento['data'])); ?></p>
                        <p class="text-sm text-gray-500"><strong>Horário:</strong> <?php echo date('H:i', strtotime($evento['horario'])); ?></p>
                        <p class="text-sm text-gray-500"><strong>Cidade:</strong> <?php echo htmlspecialchars($evento['cidade']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-center text-gray-600 mt-10">Nenhum evento encontrado para os critérios de pesquisa.</p>
    <?php endif; ?>
</div>

</body>
</html>
