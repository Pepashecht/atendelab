<?php
$usuarioLogado = usuarioAtual();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pessoas - AtendeLab</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="topo">
            <h1>Pessoas atendidas</h1>
            <div class="menu">
                <span>Olá, <?= htmlspecialchars($usuarioLogado['nome'] ?? 'Usuário') ?>!</span>
                <a href="?controller=auth&action=dashboard">Voltar</a>
                <a href="?controller=auth&action=logout">Sair</a>
            </div>
        </div>

        <div class="card">
            <h2>Nova pessoa</h2>
            <form id="pessoa-form" class="grid-form">
                <input type="hidden" name="id" id="pessoa-id">
                <div><label>Nome</label><input name="nome" required></div>
                <div><label>CPF</label><input name="cpf" required></div>
                <div><label>Telefone</label><input name="telefone" required></div>
                <div><label>Endereço</label><input name="endereco"></div>
                <div><label>Número</label><input name="end_num"></div>
                <div><label>Bairro</label><input name="bairro"></div>
                <div><label>Cidade</label><input name="cidade"></div>
                <div class="form-actions"><button type="submit">Salvar</button><button type="button" class="secondary" onclick="resetarPessoaForm()">Limpar</button></div>
            </form>
            <div id="pessoa-msg" class="message"></div>
        </div>

        <div class="card">
            <h2>Lista de pessoas</h2>
            <table>
                <thead><tr><th>ID</th><th>Nome</th><th>CPF</th><th>Telefone</th><th>Cidade</th><th>Ações</th></tr></thead>
                <tbody id="pessoas-tbody"></tbody>
            </table>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', carregarPessoas);

        document.getElementById('pessoa-form').addEventListener('submit', async function (event) {
            event.preventDefault();
            const form = new FormData(this);
            const id = form.get('id');
            const action = id ? 'atualizar' : 'criar';
            const response = await fetch(`?controller=pessoas&action=${action}`, {
                method: 'POST',
                body: new URLSearchParams(form)
            });
            const data = await response.json();
            mostrarMensagem('pessoa-msg', data.mensagem || data.erro, response.ok);
            if (response.ok) {
                resetarPessoaForm();
                carregarPessoas();
            }
        });

        async function carregarPessoas() {
            const response = await fetch('?controller=pessoas&action=listar');
            const pessoas = await response.json();
            const tbody = document.getElementById('pessoas-tbody');
            tbody.innerHTML = '';
            pessoas.forEach(pessoa => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${pessoa.id}</td>
                    <td>${pessoa.nome}</td>
                    <td>${pessoa.cpf}</td>
                    <td>${pessoa.telefone}</td>
                    <td>${pessoa.cidade}</td>
                    <td>
                        <button type="button" onclick="editarPessoa(${pessoa.id})">Editar</button>
                        <button type="button" class="danger" onclick="excluirPessoa(${pessoa.id})">Excluir</button>
                    </td>`;
                tbody.appendChild(tr);
            });
        }

        async function editarPessoa(id) {
            const response = await fetch(`?controller=pessoas&action=buscar&id=${id}`);
            const pessoa = await response.json();
            document.getElementById('pessoa-id').value = pessoa.id;
            document.querySelector('#pessoa-form input[name="nome"]').value = pessoa.nome;
            document.querySelector('#pessoa-form input[name="cpf"]').value = pessoa.cpf;
            document.querySelector('#pessoa-form input[name="telefone"]').value = pessoa.telefone;
            document.querySelector('#pessoa-form input[name="endereco"]').value = pessoa.endereco || '';
            document.querySelector('#pessoa-form input[name="end_num"]').value = pessoa.end_num || '';
            document.querySelector('#pessoa-form input[name="bairro"]').value = pessoa.bairro || '';
            document.querySelector('#pessoa-form input[name="cidade"]').value = pessoa.cidade || '';
        }

        async function excluirPessoa(id) {
            if (!confirm('Deseja excluir esta pessoa?')) return;
            const response = await fetch('?controller=pessoas&action=excluir', {
                method: 'POST',
                body: new URLSearchParams({ id })
            });
            const data = await response.json();
            mostrarMensagem('pessoa-msg', data.mensagem || data.erro, response.ok);
            if (response.ok) carregarPessoas();
        }

        function resetarPessoaForm() {
            document.getElementById('pessoa-form').reset();
            document.getElementById('pessoa-id').value = '';
        }
    </script>
</body>
</html>
