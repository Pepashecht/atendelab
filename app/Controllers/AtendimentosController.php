<?php
require_once __DIR__ . '/../Middleware/auth.php';

// Controller da entidade de Atendimentos.
// Este controller cuida apenas dos dados (JSON). A tela é aberta pelo
// FrontendController (controller=frontend&action=atendimentos).
class AtendimentosController
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

        $sql = 'SELECT
                    a.id,
                    a.data_atendimento,
                    a.status,
                    a.descricao,
                    a.pessoa_id,
                    a.tipo_atendimento_id,
                    a.usuario_id,
                    u.nome AS usuario_nome,
                    p.nome AS pessoa_nome,
                    t.nome AS tipo_nome
                FROM atendimentos a
                JOIN usuarios u ON a.usuario_id = u.id
                JOIN pessoas p ON a.pessoa_id = p.id
                JOIN tipos_atendimentos t ON a.tipo_atendimento_id = t.id
                ORDER BY a.id DESC';

        $stmt = $this->pdo->query($sql);
        $atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($atendimentos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function visualizar(): void
    {
        exigirAutenticacao();

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
                    a.descricao,
                    a.status,
                    u.nome AS usuario_nome,
                    p.nome AS pessoa_nome,
                    t.nome AS tipo_nome
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

    // O responsável pelo atendimento vem sempre da sessão do usuário
    // logado, nunca de um campo livre do formulário (Aula 006, seção 14).
    // Isso evita que a tela permita registrar um atendimento em nome de
    // outra pessoa só porque alguém alterou o valor enviado no POST.
    private function usuarioResponsavel(): ?int
    {
        if (isset($_SESSION['usuario']['id'])) {
            return (int) $_SESSION['usuario']['id'];
        }

        return null;
    }

    public function criar(): void
    {
        exigirAutenticacao();

        header('Content-Type: application/json; charset=utf-8');

        $usuario_id = $this->usuarioResponsavel();
        $pessoa_id = filter_input(INPUT_POST, 'pessoa_id', FILTER_VALIDATE_INT);
        $tipo_atendimento_id = filter_input(INPUT_POST, 'tipo_atendimento_id', FILTER_VALIDATE_INT);
        $descricao = trim($_POST['descricao'] ?? '');
        $status = trim($_POST['status'] ?? 'em_andamento');

        if (!$usuario_id) {
            http_response_code(401);
            echo json_encode(['erro' => 'Usuário não autenticado.']);
            return;
        }

        if (!$pessoa_id || !$tipo_atendimento_id || $descricao === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'Pessoa, tipo de atendimento e descrição são obrigatórios.']);
            return;
        }

        if (!in_array($status, ['em_andamento', 'concluido', 'cancelado'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido.']);
            return;
        }

        try {
            $sql = 'INSERT INTO atendimentos (usuario_id, pessoa_id, tipo_atendimento_id, descricao, status, data_atendimento)
                    VALUES (:usuario_id, :pessoa_id, :tipo_atendimento_id, :descricao, :status, NOW())';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindValue(':pessoa_id', $pessoa_id, PDO::PARAM_INT);
            $stmt->bindValue(':tipo_atendimento_id', $tipo_atendimento_id, PDO::PARAM_INT);
            $stmt->bindValue(':descricao', $descricao);
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

    public function atualizarStatus(): void
    {
        exigirAutenticacao();

        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $status = trim($_POST['status'] ?? '');

        if (!$id || $status === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'ID e o novo status são obrigatórios.']);
            return;
        }

        if (!in_array($status, ['em_andamento', 'concluido', 'cancelado'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido.']);
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
