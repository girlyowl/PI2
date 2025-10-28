<?php
include('db.php');

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

    if ($titulo && $descricao && $data && $cidade) {
        try {
            $sql = "INSERT INTO eventos (titulo, descricao, data, cidade, imagem) VALUES (:titulo, :descricao, :data, :cidade, :imagem)";
            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':data', $data);
            $stmt->bindParam(':cidade', $cidade);
            $stmt->bindParam(':imagem', $imagem);

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
