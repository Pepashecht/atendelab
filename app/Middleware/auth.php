<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function usuarioAutenticado(): bool
{
    return !empty($_SESSION['usuario']);
}

function exigirAutenticacao(): void
{
    if (!usuarioAutenticado()) {
        $_SESSION['erro_login'] = 'É necessário fazer login para acessar esta página.';
        header('Location: ?controller=auth&action=login');
        exit;
    }
}

function usuarioAtual(): array
{
    return $_SESSION['usuario'] ?? [];
}
