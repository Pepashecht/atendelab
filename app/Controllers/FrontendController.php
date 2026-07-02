<?php
require_once __DIR__ . '/../Middleware/auth.php';

// Controller responsável apenas por abrir as telas visuais.
// Ele não acessa o banco: quem busca/grava dados são PessoasController,
// TiposAtendimentosController e AtendimentosController, chamados pelo
// JavaScript (api.js) depois que a página já está carregada.
class FrontendController
{
    public function pessoas(): void
    {
        exigirAutenticacao();
        require __DIR__ . '/../Views/pessoas/index.php';
    }

    public function tiposAtendimentos(): void
    {
        exigirAutenticacao();
        require __DIR__ . '/../Views/tipos_atendimentos/index.php';
    }

    public function atendimentos(): void
    {
        exigirAutenticacao();
        require __DIR__ . '/../Views/atendimentos/index.php';
    }
}
