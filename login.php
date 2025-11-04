<?php
session_start();
include('db.php');

$erro = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario']);
    $senha = $_POST['senha'];

    // Validar se os campos não estão vazios
    if (empty($usuario) || empty($senha)) {
        $erro = "Por favor, preencha todos os campos.";
    } else {
        // Prevenir SQL Injection e XSS (Escape de caracteres)
        $usuario = htmlspecialchars($usuario);

        $sql = "SELECT * FROM usuarios WHERE usuario = :usuario LIMIT 1"; // Garantir que só um usuário será retornado
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':usuario' => $usuario]);
        $user = $stmt->fetch();

        // Verificar se o usuário foi encontrado e se a senha está correta
        if ($user && password_verify($senha, $user['senha'])) {
            $_SESSION['logado'] = true;
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_nome'] = $user['usuario'];
            $_SESSION['usuario_tipo'] = $user['tipo'];
            header('Location: index.php');
            exit;  // Evitar que o código continue executando após o redirecionamento
        } else {
            $erro = "Usuário ou senha inválidos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Login - Baixada em Cena</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen login-page">

  <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold text-center text-indigo-600 mb-6">Login</h2>

    <?php if (!empty($erro)): ?>
      <p class="text-red-600 text-sm mb-4 text-center"><?php echo $erro; ?></p>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label for="usuario" class="block text-sm font-medium text-gray-700">Usuário ou E-mail</label>
        <input type="text" name="usuario" id="usuario" required
               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
      </div>

      <div>
        <label for="senha" class="block text-sm font-medium text-gray-700">Senha</label>
        <input type="password" name="senha" id="senha" required
               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
      </div>

      <div class="flex justify-between items-center">
        <a href="index.php" class="text-sm text-indigo-600 hover:underline">← Voltar para a página inicial</a>
        <a href="register.php" class="text-sm text-indigo-600 hover:underline">Criar conta</a>
      </div>

      <button type="submit"
              class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition duration-200">
        Entrar
      </button>
    </form>
  </div>

</body>
</html>
