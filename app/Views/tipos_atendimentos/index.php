<?php
$usuarioLogado = usuarioAtual();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tipos de atendimento - AtendeLab</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="topo">
            <h1>Tipos de atendimento</h1>
            <div class="menu">
                <span>Olá, <?= htmlspecialchars($usuarioLogado['nome'] ?? 'Usuário') ?>!</span>
                <a href="?controller=auth&action=dashboard">Voltar</a>
                <a href="?controller=auth&action=logout">Sair</a>
            </div>
        </div>

        <div class="card">
            <h2>Novo tipo</h2>
            <form id="tipo-form" class="grid-form">
                <input type="hidden" name="id" id="tipo-id">
                <div><label>Nome</label><input name="nome" required></div>
                <div><label>Descrição</label><input name="descricao"></div>
                <div><label>Status</label>
                    <select name="status">
                        <option value="A">Ativo</option>
                        <option value="I">Inativo</option>
                    </select>
                </div>
                <div class="form-actions"><button type="submit">Salvar</button><button type="button" class="secondary" onclick="resetarTipoForm()">Limpar</button></div>
            </form>
            <div id="tipo-msg" class="message"></div>
        </div>

        <div class="card">
            <h2>Lista de tipos</h2>
            <table>
                <thead><tr><th>ID</th><th>Nome</th><th>Descrição</th><th>Status</th><th>Ações</th></tr></thead>
                <tbody id="tipos-tbody"></tbody>
            </table>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', carregarTipos);

        document.getElementById('tipo-form').addEventListener('submit', async function (event) {
            event.preventDefault();
            const form = new FormData(this);
            const id = form.get('id');
            const action = id ? 'atualizar' : 'criar';
            const response = await fetch(`?controller=tipos-atendimentos&action=${action}`, {
                method: 'POST',
                body: new URLSearchParams(form)
            });
            const data = await response.json();
            mostrarMensagem('tipo-msg', data.mensagem || data.erro, response.ok);
            if (response.ok) {
                resetarTipoForm();
                carregarTipos();
            }
        });

        async function carregarTipos() {
            const response = await fetch('?controller=tipos-atendimentos&action=listar');
            const tipos = await response.json();
            const tbody = document.getElementById('tipos-tbody');
            tbody.innerHTML = '';
            tipos.forEach(tipo => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${tipo.id}</td>
                    <td>${tipo.nome}</td>
                    <td>${tipo.descricao || ''}</td>
                    <td>${tipo.status}</td>
                    <td>
                        <button type="button" onclick="editarTipo(${tipo.id})">Editar</button>
                        <button type="button" class="danger" onclick="excluirTipo(${tipo.id})">Excluir</button>
                    </td>`;
                tbody.appendChild(tr);
            });
        }

        async function editarTipo(id) {
            const response = await fetch(`?controller=tipos-atendimentos&action=buscar&id=${id}`);
            const tipo = await response.json();
            document.getElementById('tipo-id').value = tipo.id;
            document.querySelector('#tipo-form input[name="nome"]').value = tipo.nome;
            document.querySelector('#tipo-form input[name="descricao"]').value = tipo.descricao || '';
            document.querySelector('#tipo-form select[name="status"]').value = tipo.status;
        }

        async function excluirTipo(id) {
            if (!confirm('Deseja excluir este tipo de atendimento?')) return;
            const response = await fetch('?controller=tipos-atendimentos&action=excluir', {
                method: 'POST',
                body: new URLSearchParams({ id })
            });
            const data = await response.json();
            mostrarMensagem('tipo-msg', data.mensagem || data.erro, response.ok);
            if (response.ok) carregarTipos();
        }

        function resetarTipoForm() {
            document.getElementById('tipo-form').reset();
            document.getElementById('tipo-id').value = '';
        }
    </script>
</body>
</html>
