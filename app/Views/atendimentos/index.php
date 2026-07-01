<?php
$usuarioLogado = usuarioAtual();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atendimentos - AtendeLab</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="topo">
            <h1>Atendimentos</h1>
            <div class="menu">
                <span>Olá, <?= htmlspecialchars($usuarioLogado['nome'] ?? 'Usuário') ?>!</span>
                <a href="?controller=auth&action=dashboard">Voltar</a>
                <a href="?controller=auth&action=logout">Sair</a>
            </div>
        </div>

        <div class="card">
            <h2>Novo atendimento</h2>
            <form id="atendimento-form" class="grid-form">
                <div><label>Usuário</label><input type="number" name="usuario_id" required></div>
                <div><label>Pessoa</label><input type="number" name="pessoa_id" required></div>
                <div><label>Tipo</label><input type="number" name="tipo_atendimento_id" required></div>
                <div><label>Status</label>
                    <select name="status">
                        <option value="em_andamento">Em andamento</option>
                        <option value="concluido">Concluído</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>
                <div class="full-width"><label>Descrição</label><textarea name="descricao" required></textarea></div>
                <div class="form-actions"><button type="submit">Salvar</button></div>
            </form>
            <div id="atendimento-msg" class="message"></div>
        </div>

        <div class="card">
            <h2>Lista de atendimentos</h2>
            <table>
                <thead><tr><th>ID</th><th>Data</th><th>Usuário</th><th>Pessoa</th><th>Tipo</th><th>Status</th><th>Descrição</th></tr></thead>
                <tbody id="atendimentos-tbody"></tbody>
            </table>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', carregarAtendimentos);

        document.getElementById('atendimento-form').addEventListener('submit', async function (event) {
            event.preventDefault();
            const response = await fetch('?controller=atendimentos&action=criar', {
                method: 'POST',
                body: new URLSearchParams(new FormData(this))
            });
            const data = await response.json();
            mostrarMensagem('atendimento-msg', data.mensagem || data.erro, response.ok);
            if (response.ok) {
                this.reset();
                carregarAtendimentos();
            }
        });

        async function carregarAtendimentos() {
            const response = await fetch('?controller=atendimentos&action=listar');
            const atendimentos = await response.json();
            const tbody = document.getElementById('atendimentos-tbody');
            tbody.innerHTML = '';
            atendimentos.forEach(atendimento => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${atendimento.id}</td>
                    <td>${atendimento.data_atendimento}</td>
                    <td>${atendimento.usuario_nome}</td>
                    <td>${atendimento.pessoa_nome}</td>
                    <td>${atendimento.tipo_atendimento}</td>
                    <td>${atendimento.status}</td>
                    <td>${atendimento.descricao}</td>`;
                tbody.appendChild(tr);
            });
        }
    </script>
</body>
</html>
