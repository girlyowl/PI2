<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];
    $usuario_tipo = $_POST['usuario_tipo'];

    $sql = "SELECT * FROM usuarios WHERE usuario = :usuario";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':usuario' => $usuario]);
    $usuario_existente = $stmt->fetch();

    if ($usuario_existente) {
        $erro = "Este nome de usuário já está em uso.";
    } else {
        $senha_hash = password_hash($senha, PASSWORD_BCRYPT);

        try {
            $sql = "INSERT INTO usuarios (usuario, senha, usuario_tipo) VALUES (:usuario, :senha, :usuario_tipo)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':usuario' => $usuario,
                ':senha' => $senha_hash,
                ':usuario_tipo' => $usuario_tipo
            ]);

            $sucesso = "Usuário cadastrado com sucesso!";
        } catch (PDOException $e) {
            $erro = "Erro ao cadastrar usuário: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">

<div class="max-w-md mx-auto mt-12 bg-white p-8 rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold text-center mb-6">Cadastro de Usuário</h1>

    <?php if (isset($erro)): ?>
        <div class="text-red-500 mb-4"><?php echo $erro; ?></div>
    <?php endif; ?>

    <?php if (isset($sucesso)): ?>
        <div class="text-green-500 mb-4"><?php echo $sucesso; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-4">
            <label for="usuario" class="block text-sm font-medium text-gray-700">Usuário:</label>
            <input type="text" name="usuario" id="usuario" class="border-2 border-gray-300 rounded-lg px-4 py-2 w-full" required>
        </div>

        <div class="mb-4">
            <label for="senha" class="block text-sm font-medium text-gray-700">Senha:</label>
            <input type="password" name="senha" id="senha" class="border-2 border-gray-300 rounded-lg px-4 py-2 w-full" required>
        </div>

        <div class="mb-4">
            <label for="usuario_tipo" class="block text-sm font-medium text-gray-700">Tipo de Usuário:</label>
            <select name="usuario_tipo" id="usuario_tipo" class="border-2 border-gray-300 rounded-lg px-4 py-2 w-full" required>
                <option value="usuario">Usuário</option>
                <option value="admin">Administrador</option>
            </select>
        </div>

        <div class="text-center">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg shadow-md hover:bg-indigo-700">Cadastrar</button>
        </div>
    </form>

    <div class="mt-4 text-center">
        <a href="index.php" class="bg-gray-300 text-indigo-600 hover:bg-indigo-100 px-6 py-2 rounded-lg shadow-md">Voltar para a Página Inicial</a>
    </div>
</div>

</body>
</html>
