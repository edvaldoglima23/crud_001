<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Pessoa</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body id="pagina-cadastro">
    <h1>Cadastro de Pessoa</h1>
    <form action="cadastro.php" method="POST">
        <!-- Campos para Nome -->
        Nome: <input type="text" name="nome" required><br>

        <!-- Campos para Documentos -->
        <h3>Documentos</h3>
        Tipo: 
        <select name="tipo_documento">
            <option value="CPF">CPF</option>
            <option value="RG">RG</option>
            <!-- Adicione mais tipos de documento se necessário -->
        </select><br>
        Número: <input type="text" name="valor_documento"><br>
        Data de Criação: <input type="date" name="data_criacao_documento"><br>
        Data de Validade: <input type="date" name="data_validade_documento"><br>

        <!-- Campos para Contatos -->
        <h3>Contatos</h3>
        Tipo: 
        <select name="tipo_contato">
            <option value="Telefone">Telefone</option>
            <option value="Email">Email</option>
            <!-- Adicione mais tipos de contato se necessário -->
        </select><br>
        Valor: <input type="text" name="valor_contato"><br>

        <!-- Campos para Endereços -->
        <h3>Endereços</h3>
        Tipo: 
        <select name="tipo_endereco">
            <option value="Residencial">Residencial</option>
            <option value="Comercial">Comercial</option>
            <!-- Adicione mais tipos de endereço se necessário -->
        </select><br>
        CEP: <input type="text" name="cep"><br>
        Estado: <input type="text" name="estado"><br>
        Cidade: <input type="text" name="cidade"><br>
        Bairro: <input type="text" name="bairro"><br>
        Rua: <input type="text" name="rua"><br>

        <!-- Div para os botões -->
        <div class="button-container">
            <a href="../index.php" class="botao-cadastro-voltar">Voltar</a>
            <input type="submit" value="Confirmar" class="botao-cadastro-confirmar">
        </div>
    </form>

    <?php
    include '../includes/db.php'; // caminho para o db.php

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Receber os dados do formulário e aplicar trim
        $nome = trim($_POST["nome"]);
        
        // Inserir dados na tabela pessoa
        $sql = "INSERT INTO pessoa (nome) VALUES ('$nome')";
        if ($conn->query($sql) === TRUE) {
            $id_pessoa = $conn->insert_id; // Obter o ID da pessoa cadastrada

            // Inserir dados na tabela documento
            $tipo_documento = trim($_POST["tipo_documento"]);
            $valor_documento = trim($_POST["valor_documento"]);
            $data_criacao_documento = trim($_POST["data_criacao_documento"]);
            $data_validade_documento = trim($_POST["data_validade_documento"]);
            $sql_documento = "INSERT INTO documento (id_pessoa, tipo, valor, data_criacao, data_validade) VALUES ('$id_pessoa', '$tipo_documento', '$valor_documento', '$data_criacao_documento', '$data_validade_documento')";
            $conn->query($sql_documento);

            // Inserir dados na tabela contato
            $tipo_contato = trim($_POST["tipo_contato"]);
            $valor_contato = trim($_POST["valor_contato"]);
            $sql_contato = "INSERT INTO contato (id_pessoa, tipo, valor) VALUES ('$id_pessoa', '$tipo_contato', '$valor_contato')";
            $conn->query($sql_contato);

            // Inserir dados na tabela endereco
            $tipo_endereco = trim($_POST["tipo_endereco"]);
            $cep = trim($_POST["cep"]);
            $estado = trim($_POST["estado"]);
            $cidade = trim($_POST["cidade"]);
            $bairro = trim($_POST["bairro"]);
            $rua = trim($_POST["rua"]);
            $sql_endereco = "INSERT INTO endereco (id_pessoa, tipo, cep, estado, cidade, bairro, rua) VALUES ('$id_pessoa', '$tipo_endereco', '$cep', '$estado', '$cidade', '$bairro', '$rua')";
            $conn->query($sql_endereco);

            echo '<div class="message-container">
                    <h2>Cadastro realizado com sucesso!</h2>
                    <p>O cadastro foi concluído e as informações foram salvas com sucesso.</p>
                    <a href="../index.php" class="button-home">Home</a>
                </div>';
        } else {
            echo "Erro: " . $sql . "<br>" . $conn->error;
        }
    }
    ?>
</body>
</html>
