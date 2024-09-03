<?php include('includes/db.php'); ?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pesquisar Pessoa</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <h1>Pesquisar Pessoa</h1>

    <form action="index.php" method="GET">
        Nome: <input type="text" name="nome" value="<?php echo isset($_GET['nome']) ? htmlspecialchars($_GET['nome']) : ''; ?>"><br>
        Documento: <input type="text" name="documento" value="<?php echo isset($_GET['documento']) ? htmlspecialchars($_GET['documento']) : ''; ?>"><br>
        Contato: <input type="text" name="contato" value="<?php echo isset($_GET['contato']) ? htmlspecialchars($_GET['contato']) : ''; ?>"><br>
        Endereço: <input type="text" name="endereco" value="<?php echo isset($_GET['endereco']) ? htmlspecialchars($_GET['endereco']) : ''; ?>"><br>
        <br>
        <input type="submit" value="Pesquisar">

        <!-- Container para os botões -->
        <div class="button-container">
            <a href="pages/cadastro.php" class="button-cadastro">Cadastrar Nova Pessoa</a>
        </div>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        // Obtém e limpa os valores dos parâmetros da URL
        $nome = isset($_GET['nome']) ? trim(preg_replace('/\s+/', ' ', $_GET['nome'])) : '';
        $documento = isset($_GET['documento']) ? trim(preg_replace('/\s+/', ' ', $_GET['documento'])) : '';
        $contato = isset($_GET['contato']) ? trim(preg_replace('/\s+/', ' ', $_GET['contato'])) : '';
        $endereco = isset($_GET['endereco']) ? trim(preg_replace('/\s+/', ' ', $_GET['endereco'])) : '';

        // Verifica se pelo menos um campo de pesquisa foi preenchido
        if (!empty($nome) || !empty($documento) || !empty($contato) || !empty($endereco)) {

            // Consulta SQL para buscar dados
            $sql = "SELECT p.id, p.nome, 
                GROUP_CONCAT(DISTINCT CONCAT(d.tipo, ' - ', d.valor) SEPARATOR ', ') AS documento,
                GROUP_CONCAT(DISTINCT CONCAT(c.tipo, ' - ', c.valor) SEPARATOR ', ') AS contato,
                GROUP_CONCAT(DISTINCT CONCAT(e.tipo, ' Cep ', e.cep, ' - ', e.estado, ' - ', e.cidade) SEPARATOR ', ') AS endereco
            FROM pessoa p
            LEFT JOIN documento d ON d.id_pessoa = p.id
            LEFT JOIN contato c ON c.id_pessoa = p.id
            LEFT JOIN endereco e ON e.id_pessoa = p.id
            WHERE 1=1";

            // Adiciona filtros baseados nos campos preenchidos
            if (!empty($nome)) {
                $nome_escapado = $conn->real_escape_string($nome);
                $sql .= " AND TRIM(REPLACE(p.nome, ' ', '')) LIKE '%" . str_replace(' ', '', $nome_escapado) . "%'";
            }
            if (!empty($documento)) {
                $documento_escapado = $conn->real_escape_string($documento);
                $sql .= " AND TRIM(REPLACE(d.valor, ' ', '')) LIKE '%" . str_replace(' ', '', $documento_escapado) . "%'";
            }
            if (!empty($contato)) {
                $contato_escapado = $conn->real_escape_string($contato);
                $sql .= " AND TRIM(REPLACE(c.valor, ' ', '')) LIKE '%" . str_replace(' ', '', $contato_escapado) . "%'";
            }
            if (!empty($endereco)) {
                $endereco_escapado = $conn->real_escape_string($endereco);
                $sql .= " AND (TRIM(REPLACE(e.cep, ' ', '')) LIKE '%" . str_replace(' ', '', $endereco_escapado) . "%'
                OR TRIM(REPLACE(e.estado, ' ', '')) LIKE '%" . str_replace(' ', '', $endereco_escapado) . "%'
                OR TRIM(REPLACE(e.cidade, ' ', '')) LIKE '%" . str_replace(' ', '', $endereco_escapado) . "%'
                OR TRIM(REPLACE(e.bairro, ' ', '')) LIKE '%" . str_replace(' ', '', $endereco_escapado) . "%'
                OR TRIM(REPLACE(e.rua, ' ', '')) LIKE '%" . str_replace(' ', '', $endereco_escapado) . "%')";
            }

            // Para garantir que cada pessoa apareça pelo menos uma vez
            $sql .= " GROUP BY p.id";

            // Executa a consulta no banco
            $result = $conn->query($sql);

            // Verifica se a consulta retornou resultados
            if ($result && $result->num_rows > 0) {
                echo "<table>";
                echo "<tr><th>ID</th><th>Nome</th><th>Documentos</th><th>Contatos</th><th>Endereços</th><th>Ações</th></tr>"; // Cabeçalho da tabela
                
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["nome"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["documento"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["contato"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["endereco"]) . "</td>";
                    echo "<td class='actions'>";
                    // Div para garantir o alinhamento dos botões
                    echo "<div class='button-group'>";
                    echo "<a href='pages/edicao.php?id=" . urlencode($row['id']) . "' class='btn btn-edit'>Editar</a>";
                    echo "<a href='pages/exclusao.php?id=" . urlencode($row['id']) . "' class='btn btn-delete'>Excluir</a>";
                    echo "</div>";
                    echo "</td>";
                    echo "</tr>";
                }
                
                echo "</table>";
            } else {
                echo "Nenhum resultado encontrado.";
            }
        }
    }
    ?>
</body>
</html>
