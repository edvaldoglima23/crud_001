<?php include('../includes/db.php'); ?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Exclusão de Pessoa</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    
    <?php
    if (isset($_GET['id'])) {
        $id_pessoa = intval($_GET['id']); // Converte para inteiro para evitar injeções

        // Excluir dados das tabelas associadas
        $sql_documento = $conn->prepare("DELETE FROM documento WHERE id_pessoa = ?");
        $sql_documento->bind_param("i", $id_pessoa);
        $sql_documento->execute();

        $sql_contato = $conn->prepare("DELETE FROM contato WHERE id_pessoa = ?");
        $sql_contato->bind_param("i", $id_pessoa);
        $sql_contato->execute();

        $sql_endereco = $conn->prepare("DELETE FROM endereco WHERE id_pessoa = ?");
        $sql_endereco->bind_param("i", $id_pessoa);
        $sql_endereco->execute();

        // Excluir o cadastro da pessoa
        $sql = $conn->prepare("DELETE FROM pessoa WHERE id = ?");
        $sql->bind_param("i", $id_pessoa);
        
        if ($sql->execute()) {
            echo "<div class='message-container'><h2>Cadastro excluído com sucesso!</h2></div>";
        } else {
            echo "<div class='message-container'><h2>Erro ao excluir: " . $conn->error . "</h2></div>";
        }

        $sql_documento->close();
        $sql_contato->close();
        $sql_endereco->close();
        $sql->close();
        $conn->close();
    }
    ?>

    <!-- Botão voltar -->
    <div class="button-container">
        <a href="../index.php" class="back-arrow">Voltar</a>
    </div>

</body>
</html>
