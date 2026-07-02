<?php
$tituloPagina = 'Dashboard';
require __DIR__ . '/../layouts/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">Dashboard</h1>
        <p class="text-secondary mb-0">Resumo dos dados reais do banco AtendeLab.</p>
    </div>
</div>

<div id="alerta"></div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-secondary small">Pessoas cadastradas</div>
                <div class="display-6 fw-semibold" id="totalPessoas">—</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-secondary small">Tipos de atendimento</div>
                <div class="display-6 fw-semibold" id="totalTipos">—</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-secondary small">Atendimentos registrados</div>
                <div class="display-6 fw-semibold" id="totalAtendimentos">—</div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h2 class="h5">Últimos atendimentos</h2>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Pessoa</th>
                        <th>Tipo</th>
                        <th>Data</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="tabelaRecentes">
                    <tr><td colspan="4" class="text-center py-4">Carregando...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h2 class="h5">Acesso rápido</h2>
        <p class="text-secondary">Use os módulos abaixo para cadastrar e consultar dados reais do banco.</p>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-success" href="<?= $baseUrl ?>?controller=frontend&action=pessoas">Gerenciar pessoas</a>
            <a class="btn btn-outline-success" href="<?= $baseUrl ?>?controller=frontend&action=tipos">Gerenciar tipos</a>
            <a class="btn btn-outline-success" href="<?= $baseUrl ?>?controller=frontend&action=atendimentos">Registrar atendimentos</a>
        </div>
    </div>
</div>

<script>
const classesStatus = {
    em_andamento: 'text-bg-warning',
    concluido: 'text-bg-success',
    cancelado: 'text-bg-secondary'
};

document.addEventListener('DOMContentLoaded', async () => {
    try {
        const resposta = await AtendeLabApi.get('dashboard', 'resumo');
        const indicadores = resposta.indicadores || {};

        document.getElementById('totalPessoas').textContent = indicadores.total_pessoas ?? 0;
        document.getElementById('totalTipos').textContent = indicadores.total_tipos ?? 0;
        document.getElementById('totalAtendimentos').textContent = indicadores.total_atendimentos ?? 0;

        const recentes = resposta.atendimentos_recentes || [];
        const tbody = document.getElementById('tabelaRecentes');

        if (!recentes.length) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4">Nenhum atendimento registrado.</td></tr>';
            return;
        }

        tbody.innerHTML = recentes.map(item => `
            <tr>
                <td>${AtendeLabApi.escape(item.pessoa_nome)}</td>
                <td>${AtendeLabApi.escape(item.tipo_nome)}</td>
                <td>${AtendeLabApi.escape(item.data_atendimento)}</td>
                <td><span class="badge ${classesStatus[item.status] || 'text-bg-primary'}">${AtendeLabApi.escape(item.status)}</span></td>
            </tr>
        `).join('');
    } catch (error) {
        AtendeLabApi.showAlert('alerta', error.message, 'danger');
        ['totalPessoas', 'totalTipos', 'totalAtendimentos'].forEach(id => {
            document.getElementById(id).textContent = '!';
        });
    }
});
</script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
