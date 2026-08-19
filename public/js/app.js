(() => {
    const searchForm = document.querySelector('#search-form');
    const results = document.querySelector('#results');
    const searchMessage = document.querySelector('#search-message');
    const budget = document.querySelector('#budget');

    const escapeHtml = (value) => String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    const showMessage = (message, type = '') => {
        searchMessage.textContent = message;
        searchMessage.className = type;
    };

    const renderBudget = (payload) => {
        const current = payload.orcamento || payload;
        const items = Array.isArray(current.itens) ? current.itens : Object.values(current.itens || {});
        if (items.length === 0) {
            budget.innerHTML = '<p>Seu orçamento está vazio.</p><strong>Total: R$ 0,00</strong>';
            return;
        }
        budget.innerHTML = items.map((item) => `<div class="budget-item"><strong>${escapeHtml(item.codigo)}</strong> - ${escapeHtml(item.nome)}<br>Qtd.: ${item.quantidade} x R$ ${escapeHtml(item.preco_unitario)} <button data-remove="${item.figurinha_id}">Remover</button></div>`).join('')
            + `<p><strong>Itens: ${current.quantidade_total}</strong></p><p><strong>Total: R$ ${escapeHtml(current.valor_total)}</strong></p>`;
        budget.querySelectorAll('[data-remove]').forEach((button) => button.addEventListener('click', () => removeItem(button.dataset.remove)));
    };

    const loadBudget = async () => {
        const response = await fetch('/orcamento');
        renderBudget(await response.json());
    };

    const addItem = async (id) => {
        try {
            const response = await fetch('/orcamento/adicionar', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ figurinha_id: Number(id) }) });
            const payload = await response.json();
            showMessage(payload.mensagem || 'Operação concluída.', response.ok ? 'success' : 'error');
            if (response.ok) renderBudget(payload);
        } catch (error) {
            showMessage('Não foi possível atualizar o orçamento.', 'error');
        }
    };

    const removeItem = async (id) => {
        try {
            const response = await fetch('/orcamento/remover', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ figurinha_id: Number(id) }) });
            const payload = await response.json();
            showMessage(payload.mensagem || 'Operação concluída.', response.ok ? 'success' : 'error');
            if (response.ok) renderBudget(payload);
        } catch (error) {
            showMessage('Não foi possível atualizar o orçamento.', 'error');
        }
    };

    searchForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        results.innerHTML = '';
        showMessage('Buscando...');
        const params = new URLSearchParams({ codigo: searchForm.codigo.value, ano: searchForm.ano.value });
        try {
            const response = await fetch(`/busca?${params}`);
            const payload = await response.json();
            if (!response.ok) { showMessage(payload.mensagem || 'Busca inválida.', 'error'); return; }
            if (!payload.resultados.length) { showMessage('Nenhuma figurinha encontrada.'); return; }
            showMessage(`${payload.resultados.length} resultado(s) encontrado(s).`, 'success');
            results.innerHTML = `<table><thead><tr><th>Código</th><th>Item</th><th>Edição</th><th>Categoria</th><th>Preço</th><th></th></tr></thead><tbody>${payload.resultados.map((item) => `<tr><td>${escapeHtml(item.codigo)}</td><td>${escapeHtml(item.nome)}<br>${escapeHtml(item.selecao)}</td><td>${escapeHtml(item.edicao_album)}</td><td>${escapeHtml(item.categoria)}</td><td>R$ ${escapeHtml(item.preco_unitario)}</td><td><button data-add="${item.id}">Adicionar ao orçamento</button></td></tr>`).join('')}</tbody></table>`;
            results.querySelectorAll('[data-add]').forEach((button) => button.addEventListener('click', () => addItem(button.dataset.add)));
        } catch (error) {
            showMessage('Não foi possível realizar a busca.', 'error');
        }
    });

    loadBudget().catch(() => { budget.textContent = 'Não foi possível carregar o orçamento.'; });
})();
