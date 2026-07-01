<?php
$usuarioLogado = usuarioAtual();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários - AtendeLab</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="topo">
            <h1>Usuários</h1>
            <div class="menu">
                <span>Olá, <?= htmlspecialchars($usuarioLogado['nome'] ?? 'Usuário') ?>!</span>
                <a href="?controller=auth&action=dashboard">Voltar</a>
                <a href="?controller=auth&action=logout">Sair</a>
            </div>
        </div>

        <div class="card">
            <h2>Novo usuário</h2>
            <form id="usuario-form" class="grid-form">
                <input type="hidden" name="id" id="usuario-id">
                <div><label>Nome</label><input name="nome" required></div>
                <div><label>E-mail</label><input type="email" name="email" required></div>
                <div><label>Senha</label><input type="password" name="senha" id="usuario-senha"></div>
                <div><label>Perfil</label>
                    <select name="perfil">
                        <option value="atendente">Atendente</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div><label>Status</label>
                    <select name="status">
                        <option value="ativo">Ativo</option>
                        <option value="inativo">Inativo</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit">Salvar</button>
                    <button type="button" class="secondary" onclick="resetarUsuarioForm()">Limpar</button>
                </div>
            </form>
            <div id="usuario-msg" class="message"></div>
        </div>

        <div class="card">
            <h2>Lista de usuários</h2>
            <table>
                <thead>
                    <tr><th>ID</th><th>Nome</th><th>E-mail</th><th>Perfil</th><th>Status</th><th>Ações</th></tr>
                </thead>
                <tbody id="usuarios-tbody"></tbody>
            </table>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', carregarUsuarios);

        document.getElementById('usuario-form').addEventListener('submit', async function (event) {
            event.preventDefault();
            const form = new FormData(this);
            const id = form.get('id');
            const action = id ? 'atualizar' : 'criar';
            const params = new URLSearchParams(form);
            const response = await fetch(`?controller=usuarios&action=${action}`, {
                method: 'POST',
                body: params
            });
            const data = await response.json();
            mostrarMensagem('usuario-msg', data.mensagem || data.erro, response.ok);
            if (response.ok) {
                resetarUsuarioForm();
                carregarUsuarios();
            }
        });

        async function carregarUsuarios() {
            const response = await fetch('?controller=usuarios&action=listar');
            const usuarios = await response.json();
            const tbody = document.getElementById('usuarios-tbody');
            tbody.innerHTML = '';
            usuarios.forEach(usuario => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${usuario.id}</td>
                    <td>${usuario.nome}</td>
                    <td>${usuario.email}</td>
                    <td>${usuario.perfil}</td>
                    <td>${usuario.status}</td>
                    <td>
                        <button type="button" onclick="editarUsuario(${usuario.id})">Editar</button>
                        <button type="button" class="danger" onclick="excluirUsuario(${usuario.id})">Excluir</button>
                    </td>`;
                tbody.appendChild(tr);
            });
        }

        async function editarUsuario(id) {
            const response = await fetch(`?controller=usuarios&action=buscar&id=${id}`);
            const usuario = await response.json();
            document.getElementById('usuario-id').value = usuario.id;
            document.querySelector('#usuario-form input[name="nome"]').value = usuario.nome;
            document.querySelector('#usuario-form input[name="email"]').value = usuario.email;
            document.querySelector('#usuario-form select[name="perfil"]').value = usuario.perfil;
            document.querySelector('#usuario-form select[name="status"]').value = usuario.status;
            document.getElementById('usuario-senha').value = '';
            document.getElementById('usuario-senha').required = false;
        }

        async function excluirUsuario(id) {
            if (!confirm('Deseja excluir este usuário?')) return;
            const response = await fetch('?controller=usuarios&action=excluir', {
                method: 'POST',
                body: new URLSearchParams({ id })
            });
            const data = await response.json();
            mostrarMensagem('usuario-msg', data.mensagem || data.erro, response.ok);
            if (response.ok) carregarUsuarios();
        }

        function resetarUsuarioForm() {
            document.getElementById('usuario-form').reset();
            document.getElementById('usuario-id').value = '';
            document.getElementById('usuario-senha').required = true;
        }
    </script>
</body>
</html>
