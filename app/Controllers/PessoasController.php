<?php
require_once __DIR__ . '/../Middleware/auth.php';

// Controller da entidade de pessoas.
// Este controller cuida apenas dos dados (JSON). A tela é aberta pelo
// FrontendController (controller=frontend&action=pessoas).
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
        exigirAutenticacao();

        header('Content-Type: application/json; charset=utf-8');

        $sql = 'SELECT id, nome, cpf, telefone, endereco, end_num, bairro, cidade, status, criado_em
                FROM pessoas
                ORDER BY id DESC';

        $stmt = $this->pdo->query($sql);
        $pessoas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($pessoas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function buscarPorId(): void
    {
        exigirAutenticacao();

        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }

        $sql = 'SELECT id, nome, cpf, telefone, endereco, end_num, bairro, cidade, status, criado_em
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
        exigirAutenticacao();

        header('Content-Type: application/json; charset=utf-8');

        $nome = trim($_POST['nome'] ?? '');
        $cpf = trim($_POST['cpf'] ?? '');
        $telefone = preg_replace('/\D/', '', trim($_POST['telefone'] ?? ''));
        $endereco = trim($_POST['endereco'] ?? '');
        $end_num = trim($_POST['end_num'] ?? '');
        $bairro = trim($_POST['bairro'] ?? '');
        $cidade = trim($_POST['cidade'] ?? '');
        $status = trim($_POST['status'] ?? 'ativo');

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

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido.']);
            return;
        }

        try {
            $sql = 'INSERT INTO pessoas (nome, cpf, telefone, endereco, end_num, bairro, cidade, status)
                    VALUES (:nome, :cpf, :telefone, :endereco, :end_num, :bairro, :cidade, :status)';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':cpf', $cpf);
            $stmt->bindValue(':telefone', $telefone);
            $stmt->bindValue(':endereco', $endereco);
            $stmt->bindValue(':end_num', $end_num);
            $stmt->bindValue(':bairro', $bairro);
            $stmt->bindValue(':cidade', $cidade);
            $stmt->bindValue(':status', $status);
            $stmt->execute();

            http_response_code(201);
            echo json_encode([
                'mensagem' => 'Pessoa cadastrada com sucesso.',
                'id' => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao cadastrar pessoa. Verifique se o CPF já está em uso.']);
        }
    }

    public function atualizar(): void
    {
        exigirAutenticacao();

        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome = trim($_POST['nome'] ?? '');
        $cpf = trim($_POST['cpf'] ?? '');
        $telefone = preg_replace('/\D/', '', trim($_POST['telefone'] ?? ''));
        $endereco = trim($_POST['endereco'] ?? '');
        $end_num = trim($_POST['end_num'] ?? '');
        $bairro = trim($_POST['bairro'] ?? '');
        $cidade = trim($_POST['cidade'] ?? '');
        $status = trim($_POST['status'] ?? 'ativo');

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

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido.']);
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
                        cidade = :cidade,
                        status = :status
                    WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':cpf', $cpf);
            $stmt->bindValue(':telefone', $telefone);
            $stmt->bindValue(':endereco', $endereco);
            $stmt->bindValue(':end_num', $end_num);
            $stmt->bindValue(':bairro', $bairro);
            $stmt->bindValue(':cidade', $cidade);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['mensagem' => 'Dados da pessoa atualizados com sucesso.'], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao atualizar dados da pessoa.']);
        }
    }

    // Regra de negócio (Aula 006): pessoas podem estar vinculadas a
    // atendimentos, então o histórico precisa ser preservado. Por isso,
    // o botão "Inativar" nunca apaga fisicamente o registro.
    public function inativar(): void
    {
        exigirAutenticacao();

        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }

        try {
            $sql = "UPDATE pessoas SET status = 'inativo' WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['mensagem' => 'Pessoa inativada com sucesso.'], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao inativar pessoa.']);
        }
    }
}
