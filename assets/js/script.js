function mostrarMensagem(elementId, texto, sucesso) {
    const container = document.getElementById(elementId);
    if (!container) return;
    container.textContent = texto || '';
    container.className = 'message ' + (sucesso ? 'success' : 'error');
}
