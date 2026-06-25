<?php
// Controller da entidade de pessoas.
class PessoasController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    public function listar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $sql = 'SELECT id, nome, cpf, telefone, endereco, end_num, bairro, cidade, criado_em
                FROM pessoas
                ORDER BY id DESC';

        $stmt = $this->pdo->query($sql);
        $pessoas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($pessoas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function buscarPorId(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }

        $sql = 'SELECT id, nome, cpf, telefone, endereco, end_num, bairro, cidade, criado_em
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

        $nome = trim($_POST['nome'] ?? '');
        $cpf = trim($_POST['cpf'] ?? '');
        $telefone = preg_replace('/\D/', '', trim($_POST['telefone'] ?? ''));
        $endereco = trim($_POST['endereco'] ?? '');
        $end_num = trim($_POST['end_num'] ?? '');
        $bairro = trim($_POST['bairro'] ?? '');
        $cidade = trim($_POST['cidade'] ?? '');

        if ($nome === '' || $cpf === '' || $telefone === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'Nome, CPF e telefone são obrigatórios.']);
            return;
        }

        if (strlen($telefone) !== 11) {
            http_response_code(400);
            echo json_encode(['erro' => 'Telefone inválido. Informe 11 dígitos.']);
            return;
        }

        try {
            $sql = 'INSERT INTO pessoas (nome, cpf, telefone, endereco, end_num, bairro, cidade)
                    VALUES (:nome, :cpf, :telefone, :endereco, :end_num, :bairro, :cidade)';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':cpf', $cpf);
            $stmt->bindValue(':telefone', $telefone);
            $stmt->bindValue(':endereco', $endereco);
            $stmt->bindValue(':end_num', $end_num);
            $stmt->bindValue(':bairro', $bairro);
            $stmt->bindValue(':cidade', $cidade);
            $stmt->execute();

            http_response_code(201);
            echo json_encode([
                'mensagem' => 'Pessoa cadastrada com sucesso.',
                'id' => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao cadastrar pessoa.']);
        }
    }

    public function atualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome = trim($_POST['nome'] ?? '');
        $cpf = trim($_POST['cpf'] ?? '');
        $telefone = preg_replace('/\D/', '', trim($_POST['telefone'] ?? ''));
        $endereco = trim($_POST['endereco'] ?? '');
        $end_num = trim($_POST['end_num'] ?? '');
        $bairro = trim($_POST['bairro'] ?? '');
        $cidade = trim($_POST['cidade'] ?? '');

        if (!$id || $nome === '' || $cpf === '' || $telefone === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'ID, nome, CPF e telefone são obrigatórios.']);
            return;
        }

        if (strlen($telefone) !== 11) {
            http_response_code(400);
            echo json_encode(['erro' => 'Telefone inválido. Informe 11 dígitos.']);
            return;
        }

        try {
            $sql = 'UPDATE pessoas
                    SET nome = :nome,
                        cpf = :cpf,
                        telefone = :telefone,
                        endereco = :endereco,
                        end_num = :end_num,
                        bairro = :bairro,
                        cidade = :cidade
                    WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':cpf', $cpf);
            $stmt->bindValue(':telefone', $telefone);
            $stmt->bindValue(':endereco', $endereco);
            $stmt->bindValue(':end_num', $end_num);
            $stmt->bindValue(':bairro', $bairro);
            $stmt->bindValue(':cidade', $cidade);
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