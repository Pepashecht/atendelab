<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AtendeLab</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7fb; margin: 0; padding: 0; }
        .container { max-width: 900px; margin: 40px auto; background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .topo { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .menu a { margin-left: 10px; color: #0d6efd; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="topo">
            <h1>Dashboard</h1>
            <div class="menu">
                <span>Olá, <?= htmlspecialchars($usuario['nome'] ?? 'Usuário') ?>!</span>
                <a href="?controller=auth&action=logout">Sair</a>
            </div>
        </div>

        <p>Bem-vindo ao sistema AtendeLab.</p>

        <div class="cards">
            <div class="metric-card">
                <h3>Pessoas</h3>
                <p class="metric-value"><?= (int) ($resumo['pessoas'] ?? 0) ?></p>
            </div>
            <div class="metric-card">
                <h3>Atendimentos</h3>
                <p class="metric-value"><?= (int) ($resumo['atendimentos'] ?? 0) ?></p>
            </div>
            <div class="metric-card">
                <h3>Tipos</h3>
                <p class="metric-value"><?= (int) ($resumo['tipos'] ?? 0) ?></p>
            </div>
        </div>

        <ul>
            <li><a href="?controller=usuarios&action=listar&view=html">Gerenciar usuários</a></li>
            <li><a href="?controller=pessoas&action=listar&view=html">Gerenciar pessoas</a></li>
            <li><a href="?controller=atendimentos&action=listar&view=html">Gerenciar atendimentos</a></li>
            <li><a href="?controller=tipos-atendimentos&action=listar&view=html">Gerenciar tipos de atendimento</a></li>
        </ul>
    </div>
</body>
</html>
