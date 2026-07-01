<?php

require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/app/Controllers/UsuarioController.php';
require_once __DIR__ . '/app/Controllers/PessoasController.php';
require_once __DIR__ . '/app/Controllers/AtendimentosController.php';
require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';

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
            case 'resumo':
                $authController->resumo();
                break;
            case 'logout':
                $authController->logout();
                break;
            default:
                $authController->exibirLogin();
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
                echo 'Ação de usuário não encontrada.';
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
                $pessoasController->buscarPorId();
                break;
            case 'criar':
                $pessoasController->criar();
                break;
            case 'atualizar':
                $pessoasController->atualizar();
                break;
            case 'excluir':
                $pessoasController->excluir();
                break;
            default:
                echo 'Ação de pessoa não encontrada.';
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
            case 'atualizar-status':
                $atendimentosController->atualizarStatus();
                break;
            default:
                echo 'Ação de atendimento não encontrada.';
                break;
        }
        break;

    case 'tipos-atendimentos':
    case 'tipos_atendimentos':
        $tiposAtendimentosController = new TiposAtendimentosController();

        switch ($action) {
            case 'listar':
                $tiposAtendimentosController->listar();
                break;
            case 'buscar':
                $tiposAtendimentosController->buscarPorId();
                break;
            case 'criar':
                $tiposAtendimentosController->criar();
                break;
            case 'atualizar':
                $tiposAtendimentosController->atualizar();
                break;
            case 'excluir':
                $tiposAtendimentosController->excluir();
                break;
            default:
                echo 'Ação de tipo de atendimento não encontrada.';
                break;
        }
        break;

    default:
        echo '<h1>AtendeLab</h1>';
        echo '<p>Projeto em execução. Acesse <a href="?controller=auth&action=login">o login</a> para começar.</p>';
        break;
}