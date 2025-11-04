<?php  
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    echo "Acesso negado. Faça login para acessar esta página.";
    exit;
}

include('db.php');

$usuario_id = $_SESSION['usuario_id'];
$usuario_nome = $_SESSION['usuario_nome'];

$stmt = $pdo->prepare("SELECT * FROM eventos WHERE usuario_id = :usuario_id ORDER BY (data < CURDATE()), data ASC");
$stmt->execute([':usuario_id' => $usuario_id]);
$eventos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Eventos - Eventos Baixada Santista</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100">

<header class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white p-6">
  <div class="max-w-6xl mx-auto flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-extrabold">Gerenciar Meus Eventos</h1>
    <p class="text-sm mt-1">Bem-vindo, <span class="font-semibold"><?php echo h($usuario_nome); ?></span>!</p>
    </div>

    <div class="flex items-center space-x-4">
        <a href="criar-eventos.php" class="bg-white text-indigo-600 px-4 py-2 rounded-lg shadow-md hover:bg-indigo-100 transition-all">
            + Criar Novo Evento
        </a>
        <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-red-600 transition-all">
            Sair
        </a>
    </div>
  </div>
</header>

<div class="max-w-6xl mx-auto p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Meus Eventos</h2>

    <?php if ($eventos): ?>
        <?php foreach ($eventos as $evento): ?>
            <div class="bg-white p-4 shadow rounded-lg mb-4 flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800"><?php echo h($evento['titulo']); ?></h2>
                    <p class="text-sm text-gray-600">
                        <?php echo date('d/m/Y', strtotime($evento['data'])); ?> às 
                        <?php echo date('H:i', strtotime($evento['horario'])); ?> em 
                        <?php echo h($evento['cidade']); ?>
                    </p>
                </div>
                <div class="flex space-x-2">
                    <a href="editar-evento.php?id=<?php echo $evento['id']; ?>" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition-all">Editar</a>
                    <a href="excluir-evento.php?id=<?php echo $evento['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir este evento?');" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-all">Excluir</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="bg-white p-6 rounded-lg shadow text-center">
            <p class="text-lg text-gray-700 font-medium mb-4">Você ainda não criou nenhum evento.</p>
            <a href="criar-eventos.php" class="inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg shadow-md hover:bg-indigo-700 transition-all">
                + Criar Meu Primeiro Evento
            </a>
        </div>
    <?php endif; ?>
</div>

<div class="max-w-6xl mx-auto p-6 mt-6 text-center">
    <a href="index.php" class="bg-indigo-600 text-white px-6 py-2 rounded-lg shadow-md hover:bg-indigo-700 transition-all">
        Voltar para a Página Inicial
    </a>
</div>

</body>
</html>
