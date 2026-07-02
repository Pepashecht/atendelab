<?php
require_once __DIR__ . '/../Middleware/auth.php';

// Controller responsável apenas pelo resumo de indicadores do dashboard.
// A página em si (HTML) é servida por AuthController::dashboard().
// Este controller só devolve dados em JSON, consumidos pelo JavaScript
// da view app/Views/dashboard/index.php, conforme o fluxo descrito na
// Aula 006 (view -> api.js -> routes.php -> controller -> PDO -> banco).
class DashboardController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    public function resumo(): void
    {
        exigirAutenticacao();

        header('Content-Type: application/json; charset=utf-8');

        $totalPessoas = (int) $this->pdo->query('SELECT COUNT(*) FROM pessoas')->fetchColumn();
        $totalTipos = (int) $this->pdo->query('SELECT COUNT(*) FROM tipos_atendimentos')->fetchColumn();
        $totalAtendimentos = (int) $this->pdo->query('SELECT COUNT(*) FROM atendimentos')->fetchColumn();

        $sqlRecentes = "SELECT
                a.id,
                a.data_atendimento,
                a.status,
                p.nome AS pessoa_nome,
                t.nome AS tipo_nome
            FROM atendimentos a
            JOIN pessoas p ON a.pessoa_id = p.id
            JOIN tipos_atendimentos t ON a.tipo_atendimento_id = t.id
            ORDER BY a.id DESC
            LIMIT 5";

        $recentes = $this->pdo->query($sqlRecentes)->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'indicadores' => [
                'total_pessoas' => $totalPessoas,
                'total_tipos' => $totalTipos,
                'total_atendimentos' => $totalAtendimentos,
            ],
            'atendimentos_recentes' => $recentes,
        ], JSON_UNESCAPED_UNICODE);
    }
}
