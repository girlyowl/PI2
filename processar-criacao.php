<?php
session_start();
include('db.php');

// Protege endpoint: somente usuários autenticados podem criar eventos por aqui
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    // Retornar 403 em requisições automatizadas
    http_response_code(403);
    echo "Acesso negado. Faça login para criar eventos.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
    $descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';
    $data = isset($_POST['data']) ? $_POST['data'] : '';
    $cidade = isset($_POST['cidade']) ? trim($_POST['cidade']) : '';

    $imagem = null;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
        $imagemTmp = $_FILES['imagem']['tmp_name'];
        $imagemNome = $_FILES['imagem']['name'];
        $imagemDir = 'uploads/';
        $imagemExt = pathinfo($imagemNome, PATHINFO_EXTENSION);

        $imagemNomeUnico = uniqid() . '.' . $imagemExt;

        if (move_uploaded_file($imagemTmp, $imagemDir . $imagemNomeUnico)) {
            $imagem = $imagemDir . $imagemNomeUnico;
        }
    }

    $usuario_id = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : null;

    if ($titulo && $descricao && $data && $cidade && $usuario_id) {
        try {
            // Inclui usuario_id para rastreabilidade e evita inserções anônimas
            $sql = "INSERT INTO eventos (titulo, descricao, data, cidade, imagem, usuario_id) VALUES (:titulo, :descricao, :data, :cidade, :imagem, :usuario_id)";
            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':data', $data);
            $stmt->bindParam(':cidade', $cidade);
            $stmt->bindParam(':imagem', $imagem);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);

            $stmt->execute();

            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            echo "Erro ao inserir o evento: " . $e->getMessage();
        }
    } else {
        echo "Todos os campos são obrigatórios!";
    }
}
?>
