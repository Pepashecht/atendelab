<?php
// Controller da entidade de Atendimentos.
class AtendimentosController
{
    private $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    // Listar com JOIN, conforme exigido na atividade.
    public function listar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        // O JOIN traz os nomes do usuário, da pessoa e a descrição do tipo, em vez de apenas os IDs.
        $sql = 'SELECT 
                    a.id, 
                    a.data_atendimento, 
                    a.status,
                    u.nome AS usuario_nome,
                    p.nome AS pessoa_nome,
                    t.descricao AS tipo_atendimento
                FROM atendimentos a
                JOIN usuarios u ON a.usuario_id = u.id
                JOIN pessoas p ON a.pessoa_id = p.id
                JOIN tipos_atendimentos t ON a.tipo_atendimento_id = t.id
                ORDER BY a.id DESC';

        $stmt = $this->pdo->query($sql);
        $atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($atendimentos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    // Correspondente ao "visualizar" solicitado pelo professor.
    public function visualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }

        $sql = 'SELECT 
                    a.id, 
                    a.data_atendimento, 
                    a.observacoes,
                    a.status,
                    u.nome AS usuario_nome,
                    p.nome AS pessoa_nome,
                    t.descricao AS tipo_atendimento
                FROM atendimentos a
                JOIN usuarios u ON a.usuario_id = u.id
                JOIN pessoas p ON a.pessoa_id = p.id
                JOIN tipos_atendimentos t ON a.tipo_atendimento_id = t.id
                WHERE a.id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$atendimento) {
            http_response_code(404);
            echo json_encode(['erro' => 'Atendimento não encontrado.']);
            return;
        }

        echo json_encode($atendimento, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function criar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $usuario_id = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT);
        $pessoa_id = filter_input(INPUT_POST, 'pessoa_id', FILTER_VALIDATE_INT);
        $tipo_atendimento_id = filter_input(INPUT_POST, 'tipo_atendimento_id', FILTER_VALIDATE_INT);
        $observacoes = trim($_POST['observacoes'] ?? '');
        $status = trim($_POST['status'] ?? 'aberto'); // ex: aberto, em_andamento, concluido

        if (!$usuario_id || !$pessoa_id || !$tipo_atendimento_id) {
            http_response_code(400);
            echo json_encode(['erro' => 'IDs de usuário, pessoa e tipo de atendimento são obrigatórios.']);
            return;
        }

        try {
            $sql = 'INSERT INTO atendimentos (usuario_id, pessoa_id, tipo_atendimento_id, observacoes, status, data_atendimento) 
                    VALUES (:usuario_id, :pessoa_id, :tipo_atendimento_id, :observacoes, :status, NOW())';
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindValue(':pessoa_id', $pessoa_id, PDO::PARAM_INT);
            $stmt->bindValue(':tipo_atendimento_id', $tipo_atendimento_id, PDO::PARAM_INT);
            $stmt->bindValue(':observacoes', $observacoes);
            $stmt->bindValue(':status', $status);
            $stmt->execute();

            http_response_code(201);
            echo json_encode([
                'mensagem' => 'Atendimento registrado com sucesso.',
                'id' => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao registrar atendimento. Verifique se os IDs fornecidos existem.']);
        }
    }

    // Correspondente ao "atualizar status" solicitado pelo professor.
    public function atualizarStatus(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $status = trim($_POST['status'] ?? '');

        if (!$id || $status === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'ID e o novo status são obrigatórios.']);
            return;
        }

        try {
            $sql = 'UPDATE atendimentos SET status = :status WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['mensagem' => 'Status do atendimento atualizado com sucesso.'], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao atualizar status do atendimento.']);
        }
    }
}