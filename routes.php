<?php

require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/app/Controllers/UsuarioController.php';
require_once __DIR__ . '/app/Controllers/PessoasController.php';
require_once __DIR__ . '/app/Controllers/AtendimentosController.php';
require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';
require_once __DIR__ . '/app/Controllers/DashboardController.php';
require_once __DIR__ . '/app/Controllers/FrontendController.php';

$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

switch ($controller) {
    case 'auth':
        $authController = new AuthController();

        switch ($action) {
            case 'login':
                $authController->exibirLogin();
                break;
            case 'entrar':
                $authController->entrar();
                break;
            case 'dashboard':
                $authController->dashboard();
                break;
            case 'logout':
                $authController->logout();
                break;
            default:
                $authController->exibirLogin();
                break;
        }
        break;

    // Controller exclusivamente de dados (JSON) para o dashboard.
    // A página em si é aberta por auth&action=dashboard.
    case 'dashboard':
        $dashboardController = new DashboardController();

        switch ($action) {
            case 'resumo':
                $dashboardController->resumo();
                break;
            default:
                http_response_code(404);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['erro' => 'Ação de dashboard não encontrada.']);
                break;
        }
        break;

    // Controller exclusivamente de páginas visuais (sem acesso a banco).
    // As operações de dados de cada tela continuam em pessoas, tipos e
    // atendimentos, chamadas pelo JavaScript (api.js).
    case 'frontend':
        $frontendController = new FrontendController();

        switch ($action) {
            case 'pessoas':
                $frontendController->pessoas();
                break;
            case 'tipos':
                $frontendController->tiposAtendimentos();
                break;
            case 'atendimentos':
                $frontendController->atendimentos();
                break;
            default:
                http_response_code(404);
                echo 'Página não encontrada.';
                break;
        }
        break;

    case 'usuarios':
        $usuarioController = new UsuarioController();

        switch ($action) {
            case 'listar':
                $usuarioController->listar();
                break;
            case 'buscar':
            case 'buscarPorId':
                $usuarioController->buscarPorId();
                break;
            case 'criar':
                $usuarioController->criar();
                break;
            case 'atualizar':
                $usuarioController->atualizar();
                break;
            case 'excluir':
                $usuarioController->excluir();
                break;
            default:
                http_response_code(404);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['erro' => 'Ação de usuário não encontrada.']);
                break;
        }
        break;

    case 'pessoas':
        $pessoasController = new PessoasController();

        switch ($action) {
            case 'listar':
                $pessoasController->listar();
                break;
            case 'buscar':
            case 'buscarPorId':
                $pessoasController->buscarPorId();
                break;
            case 'criar':
                $pessoasController->criar();
                break;
            case 'atualizar':
                $pessoasController->atualizar();
                break;
            case 'inativar':
                $pessoasController->inativar();
                break;
            default:
                http_response_code(404);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['erro' => 'Ação de pessoa não encontrada.']);
                break;
        }
        break;

    case 'atendimentos':
        $atendimentosController = new AtendimentosController();

        switch ($action) {
            case 'listar':
                $atendimentosController->listar();
                break;
            case 'visualizar':
                $atendimentosController->visualizar();
                break;
            case 'criar':
                $atendimentosController->criar();
                break;
            case 'atualizarStatus':
            case 'atualizar-status':
                $atendimentosController->atualizarStatus();
                break;
            default:
                http_response_code(404);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['erro' => 'Ação de atendimento não encontrada.']);
                break;
        }
        break;

    // Aceita tanto "tipos" (nome usado nas telas/api.js, conforme o
    // material da Aula 006) quanto os nomes antigos "tipos-atendimentos"
    // e "tipos_atendimentos", para não quebrar links já existentes.
    case 'tipos':
    case 'tipos-atendimentos':
    case 'tipos_atendimentos':
        $tiposAtendimentosController = new TiposAtendimentosController();

        switch ($action) {
            case 'listar':
                $tiposAtendimentosController->listar();
                break;
            case 'buscar':
            case 'buscarPorId':
                $tiposAtendimentosController->buscarPorId();
                break;
            case 'criar':
                $tiposAtendimentosController->criar();
                break;
            case 'atualizar':
                $tiposAtendimentosController->atualizar();
                break;
            case 'inativar':
                $tiposAtendimentosController->inativar();
                break;
            default:
                http_response_code(404);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['erro' => 'Ação de tipo de atendimento não encontrada.']);
                break;
        }
        break;

    default:
        echo '<h1>AtendeLab</h1>';
        echo '<p>Projeto em execução. Acesse <a href="?controller=auth&action=login">o login</a> para começar.</p>';
        break;
}
