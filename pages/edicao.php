<?php include('../includes/db.php'); ?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Edição de Pessoa</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body id="pagina-cadastro">
    <h1>Edição de Pessoa</h1>

    <?php
    $edit_completed = false;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Remove espaços em branco dos dados recebidos
        $id_pessoa = trim($_POST["id"]);
        $nome = trim($_POST["nome"]);

        // Documentos
        $tipo_documento = $_POST["tipo_documento"];
        $valor_documento = $_POST["valor_documento"];
        $data_criacao_documento = $_POST["data_criacao_documento"];
        $data_validade_documento = $_POST["data_validade_documento"];

        // Contatos
        $tipo_contato = $_POST["tipo_contato"];
        $valor_contato = $_POST["valor_contato"];

        // Endereços
        $tipo_endereco = $_POST["tipo_endereco"];
        $cep = $_POST["cep"];
        $estado = $_POST["estado"];
        $cidade = $_POST["cidade"];
        $bairro = $_POST["bairro"];
        $rua = $_POST["rua"];

        // Início da transação
        $conn->query("START TRANSACTION");

        try {
            // Atualizar dados da pessoa
            $sql_pessoa = "UPDATE pessoa SET nome='$nome' WHERE id='$id_pessoa'";
            if (!$conn->query($sql_pessoa)) {
                throw new Exception("Erro ao atualizar dados da pessoa: " . $conn->error);
            }

            // Atualizar ou inserir documentos

            $conn->query("DELETE FROM documento WHERE id_pessoa = $id_pessoa");
            for ($i=0; $i < count($tipo_documento); $i++) {
                $sql_documento = "INSERT INTO documento (id_pessoa, tipo, valor, data_criacao, data_validade)
                                  VALUES ('$id_pessoa', '$tipo_documento[$i]', '$valor_documento[$i]', '$data_criacao_documento[$i]', '$data_validade_documento[$i]')
                                  ON DUPLICATE KEY UPDATE valor='$valor_documento[$i]', data_criacao='$data_criacao_documento[$i]', data_validade='$data_validade_documento[$i]'";
                if (!$conn->query($sql_documento)) {
                    throw new Exception("Erro ao atualizar documentos: " . $conn->error);
                }
            }

            // Atualizar ou inserir contatos
            $conn->query("DELETE FROM contato WHERE id_pessoa = $id_pessoa");
            for ($i=0; $i < count($tipo_contato); $i++) {
                $sql_contato = "INSERT INTO contato (id_pessoa, tipo, valor)
                                VALUES ('$id_pessoa', '$tipo_contato[$i]', '$valor_contato[$i]')
                                ON DUPLICATE KEY UPDATE valor='$valor_contato[$i]'";
                if (!$conn->query($sql_contato)) {
                    throw new Exception("Erro ao atualizar contatos: " . $conn->error);
                }
            }

            // Atualizar ou inserir endereços
            $conn->query("DELETE FROM endereco WHERE id_pessoa = $id_pessoa");
            for ($i=0; $i < count($tipo_endereco); $i++) {
                $sql_endereco = "INSERT INTO endereco (id_pessoa, tipo, cep, estado, cidade, bairro, rua)
                                VALUES ('$id_pessoa', '$tipo_endereco[$i]', '$cep[$i]', '$estado[$i]', '$cidade[$i]', '$bairro[$i]', '$rua[$i]')
                                ON DUPLICATE KEY UPDATE cep='$cep[$i]', estado='$estado[$i]', cidade='$cidade[$i]', bairro='$bairro[$i]', rua='$rua[$i]'";
                if (!$conn->query($sql_endereco)) {
                    throw new Exception("Erro ao atualizar endereços: " . $conn->error);
                }
            }

            // Commit da transação
            $conn->query("COMMIT");
            $edit_completed = true;

            echo "<div class='message-container'>
                    <h2>Edição concluída com sucesso!</h2>
                </div>";
        } catch (Exception $e) {
            // Rollback da transação em caso de erro
            $conn->query("ROLLBACK");
            echo "<div class='message-container'>
                    <h2>Erro na edição: " . $e->getMessage() . "</h2>
                </div>";
        }
    } else if (isset($_GET['id'])) {
        $id_pessoa = trim($_GET['id']);

        // Consultar dados da pessoa
        $sql_pessoa = "SELECT * FROM pessoa WHERE id = $id_pessoa";
        $result_pessoa = $conn->query($sql_pessoa);
        $pessoa = $result_pessoa->fetch_assoc();
    }
    ?>

    <?php if (!$edit_completed && isset($pessoa)): ?>
        <form action="edicao.php?id=<?php echo htmlspecialchars($id_pessoa); ?>" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($pessoa['id']); ?>">

            <!-- Campo Nome -->
            Nome: <input type="text" name="nome" value="<?php echo htmlspecialchars($pessoa['nome']); ?>" ><br>

            <!-- Campos para Documentos -->
            <h3>Documentos</h3>
            <?php $sql_documento = "SELECT * FROM documento WHERE id_pessoa = $id_pessoa";
                    $result_documento = $conn->query($sql_documento);
                    $documento = $result_documento->fetch_assoc();
                    while ($contato = $result_documento->fetch_assoc()) {?>
                        Tipo:
                        <select name="tipo_documento[]">
                            <option value="CPF" <?php if (isset($documento['tipo']) && $documento['tipo'] == 'CPF') echo 'selected'; ?>>CPF</option>
                            <option value="RG" <?php if (isset($documento['tipo']) && $documento['tipo'] == 'RG') echo 'selected'; ?>>RG</option>
                        </select><br>
                        Numero: <input type="text" name="valor_documento[]" value="<?php echo isset($documento['valor']) ? htmlspecialchars($documento['valor']) : ''; ?>"><br>
                        Data de Criação: <input type="date" name="data_criacao_documento[]" value="<?php echo isset($documento['data_criacao']) ? htmlspecialchars($documento['data_criacao']) : ''; ?>"><br>
                        Data de Validade: <input type="date" name="data_validade_documento[]" value="<?php echo isset($documento['data_validade']) ? htmlspecialchars($documento['data_validade']) : ''; ?>"><br>              
                    <?php } ?>
            <!-- Campos para Contatos -->
            <h3>Contatos</h3>
            <?php
            $sql_contato = "SELECT * FROM contato WHERE id_pessoa = $id_pessoa";
            $result_contato = $conn->query($sql_contato);
            while ($contato = $result_contato->fetch_assoc()) {?>

                Tipo:
                <select name="tipo_contato[]">
                <option value="Telefone" <?php if (isset($contato['tipo']) && $contato['tipo'] == 'Telefone') echo 'selected'; ?>>Telefone</option>
                <option value="Email" <?php if (isset($contato['tipo']) && $contato['tipo'] == 'Email') echo 'selected'; ?>>Email</option>
            </select><br>
            <input type="text" name="valor_contato[]" value="<?php echo isset($contato['valor']) ? htmlspecialchars($contato['valor']) : ''; ?>"><br>

            <?php } ?>
            
            <!-- Campos para Endereços -->
            <h3>Endereços</h3>
            <?php
            $sql_endereco = "SELECT * FROM endereco WHERE id_pessoa = $id_pessoa";
            $result_endereco = $conn->query($sql_endereco);
            while ($endereco = $result_endereco->fetch_assoc()) { ?>
                Tipo:
                <select name="tipo_endereco[]">
                    <option value="Casa" <?php if (isset($endereco['tipo']) && $endereco['tipo'] == 'Casa') echo 'selected'; ?>>Casa</option>
                    <option value="Trabalho" <?php if (isset($endereco['tipo']) && $endereco['tipo'] == 'Trabalho') echo 'selected'; ?>>Trabalho</option>
                    <option value="Outro" <?php if (isset($endereco['tipo']) && $endereco['tipo'] == 'Outro') echo 'selected'; ?>>Outro</option>
                </select><br>
                CEP: <input type="text" name="cep[]" value="<?php echo isset($endereco['cep']) ? htmlspecialchars($endereco['cep']) : ''; ?>" ><br>
                Estado: <input type="text" name="estado[]" value="<?php echo isset($endereco['estado']) ? htmlspecialchars($endereco['estado']) : ''; ?>"><br>
                Cidade: <input type="text" name="cidade[]" value="<?php echo isset($endereco['cidade']) ? htmlspecialchars($endereco['cidade']) : ''; ?>"><br>
                Bairro: <input type="text" name="bairro[]" value="<?php echo isset($endereco['bairro']) ? htmlspecialchars($endereco['bairro']) : ''; ?>"><br>
                Rua: <input type="text" name="rua[]" value="<?php echo isset($endereco['rua']) ? htmlspecialchars($endereco['rua']) : ''; ?>"><br>
            <?php } ?>


            <input type="submit" value="Salvar">

            <div class="button-container">
            <a href="../index.php" class="botao-editar-voltar">Voltar</a>
            </div>
        </form>
    <?php endif; ?>

    <?php if ($edit_completed): ?>
        <!-- Botão Voltar aparece após a edição ser concluída -->
        <a href="../index.php" class="back-arrow">Voltar</a>
    <?php endif; ?>

    <a href="../index.php"></a>
</body>
</html>
