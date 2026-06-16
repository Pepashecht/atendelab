<?php
// Controller da entidade de pessoas.
// Em uma arquitetura MVC, ele recebe a requisição, valida dados e acessa o banco.
class PessoasController
{
    // Conexão PDO reutilizada em todos os métodos.
    private $pdo;

    public function __construct()
    {
        // Importa o arquivo que inicializa o objeto $pdo.
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    public function listar(): void
    {
        // Define saída em JSON para APIs/consumo por front-end.
        header('Content-Type: application/json; charset=utf-8');

        // Consulta todas as pessoas com ordenação decrescente por ID.
        // ATENÇÃO: Ajuste os nomes das colunas conforme o seu banco de dados.
        $sql = 'SELECT id, nome, cpf, email, telefone 
                FROM pessoas 
                ORDER BY id DESC';

        $stmt = $this->pdo->query($sql);
        $pessoas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // JSON_PRETTY_PRINT melhora leitura em desenvolvimento.
        echo json_encode($pessoas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function buscarPorId(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        // Lê e valida o ID recebido por GET.
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }

        // Consulta parametrizada evita SQL Injection.
        $sql = 'SELECT id, nome, cpf, email, telefone 
                FROM pessoas 
                WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pessoa) {
            http_response_code(404);
            echo json_encode(['erro' => 'Pessoa não encontrada.']);
            return;
        }

        echo json_encode($pessoa, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function criar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        // Coleta dados do formulário (POST).
        $nome = trim($_POST['nome'] ?? '');
        $cpf = trim($_POST['cpf'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');

        // Regras mínimas de validação de entrada.
        if ($nome === '' || $cpf === '' || $email === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'Nome, CPF e e-mail são obrigatórios.']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['erro' => 'E-mail inválido.']);
            return;
        }

        try {
            $sql = 'INSERT INTO pessoas (nome, cpf, email, telefone) 
                    VALUES (:nome, :cpf, :email, :telefone)';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':cpf', $cpf);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':telefone', $telefone);
            $stmt->execute();

            http_response_code(201);
            echo json_encode([
                'mensagem' => 'Pessoa cadastrada com sucesso.',
                'id' => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            // Em produção, registre $e em log vez de expor detalhes.
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao cadastrar pessoa.']);
        }
    }

    public function atualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        // ID vem no POST para operação de update.
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome = trim($_POST['nome'] ?? '');
        $cpf = trim($_POST['cpf'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');

        if (!$id || $nome === '' || $cpf === '' || $email === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'ID, nome, CPF e e-mail são obrigatórios.']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['erro' => 'E-mail inválido.']);
            return;
        }

        try {
            $sql = 'UPDATE pessoas 
                    SET nome = :nome, 
                        cpf = :cpf, 
                        email = :email, 
                        telefone = :telefone 
                    WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':cpf', $cpf);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':telefone', $telefone);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['mensagem' => 'Dados da pessoa atualizados com sucesso.'], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao atualizar dados da pessoa.']);
        }
    }

    public function excluir(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        // Exclusão por ID recebido no corpo da requisição.
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }

        try {
            $sql = 'DELETE FROM pessoas WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['mensagem' => 'Registro excluído com sucesso.'], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao excluir registro.']);
        }
    }
}