<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AtendeLab</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7fb; margin: 0; padding: 0; }
        .box { max-width: 380px; margin: 80px auto; background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        h1 { margin-top: 0; }
        label { display: block; margin-top: 12px; font-weight: bold; }
        input { width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; margin-top: 16px; padding: 10px; background: #0d6efd; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        .erro { color: #b42318; background: #fef3f2; padding: 10px; border-radius: 4px; margin-bottom: 12px; }
        .mensagem { color: #027a48; background: #ecfdf3; padding: 10px; border-radius: 4px; margin-bottom: 12px; }
    </style>
</head>
<body>
    <div class="box">
        <h1>AtendeLab</h1>
        <?php if (!empty($erro)): ?><div class="erro"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
        <?php if (!empty($mensagem)): ?><div class="mensagem"><?= htmlspecialchars($mensagem) ?></div><?php endif; ?>
        <form method="post" action="?controller=auth&action=entrar">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required>

            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" required>

            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>
